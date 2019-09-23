<?php

/**
 * Connect to the TagPlus API via OAuth 2
 *
 * @author Gustavo Sousa <oi@gustavosousa.me>
 */

namespace App\Tagplus;

use App\Models\Config;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

// use \TypeRocket\Models\Model;

/**
 * Class Tagplus
 *
 * @package App\Models
 * @link    https://developers.tagplus.com.br/
 * @link    http://oauth2-client.thephpleague.com/
 */
class Tagplus
{
    const WEB_HOOK_SECRET = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    protected $apiVersion = '2.0';
    protected $clientId = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    protected $clientSecret = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    protected $urlAuthorize = 'https://developers.tagplus.com.br/authorize';
    protected $urlAccessToken = 'https://api.tagplus.com.br/oauth2/token';
    protected $requestUrl = 'https://api.tagplus.com.br';
    protected $scope = [
        'read:produtos',
        'read:categorias',
        'read:clientes',
        'write:clientes',
        'read:pedidos',
        'write:pedidos',
    ];
    /* @var Provider $provider */
    protected $provider;
    protected $accessToken;

    /**
     * Tagplus constructor.
     */
    public function __construct()
    {
        $this->provider = new Provider(
            [
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                // 'redirectUri' => null,
                'urlAuthorize' => $this->urlAuthorize,
                'urlAccessToken' => $this->urlAccessToken,
                'urlResourceOwnerDetails' => $this->requestUrl,
            ]
        );
    }

    /**
     * Returns True if a valid access token exists
     *
     * @return bool
     */
    public function accessTokenIsValid()
    {
        $accessToken = $this->getAccessToken();

        if (!method_exists($accessToken, 'hasExpired')) {
            return false;
        }

        if ($accessToken->hasExpired()) {
            try {
                $newAccessToken = $this->provider->getAccessToken(
                    'refresh_token',
                    ['refresh_token' => $accessToken->getRefreshToken()]
                );
            } catch (IdentityProviderException $e) {
                return false;
            }

            $accessToken = $newAccessToken;
            $this->setAccessToken($accessToken);
        }

        return !$accessToken->hasExpired();
    }

    /**
     * Get saved access token
     *
     * @return AccessToken
     */
    public function getAccessToken()
    {
        if (!$this->accessToken) {
            $this->accessToken = Config::getOption('access_token');
        }
        return $this->accessToken;
    }

    /**
     * Set access token on object and database
     *
     * @param AccessTokenInterface $accessToken Access Token
     *
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        Config::setOption('access_token', $accessToken);
    }

    /**
     * Button to authentication, generating a new access token
     *
     * @param bool $echo True to write directly in html
     *
     * @return string
     */
    public function buttonToAuth($echo = true)
    {
        $html = sprintf(
            '<form action="%s" method="POST">%s</form>',
            menu_page_url(Config::MENU_PAGE_SLUG, false),
            get_submit_button('Conectar ao TagPlus', 'primary', 'Auth')
        );

        if ($echo) {
            echo $html;
        }

        return $html;
    }

    /**
     * Button to revoke existing access token
     *
     * @param bool $echo True to write directly in html
     *
     * @return string
     */
    public function buttonToRevokeAccess($echo = true)
    {
        $html = get_submit_button('Revogar acesso', 'delete', 'RevokeAccess', false);

        if ($echo) {
            echo $html;
        }

        return $html;
    }

    /**
     * Get authorization via OAuth 2
     *
     * @return void
     */
    public function getAuthorization()
    {
        if (!isset($_GET['code'])) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $this->provider->getAuthorizationUrl(
                [
                    'scope' => implode(' ', $this->scope),
                ]
            );

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $this->provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

            // Check given state against previously stored one
            // to mitigate CSRF attack
        } elseif (empty($_GET['state'])
            || @($_GET['state'] !== $_SESSION['oauth2state'])
        ) {

            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $this->provider->getAccessToken(
                    'authorization_code',
                    ['code' => $_GET['code']]
                );

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                // echo $accessToken->getToken() . "\n";
                // echo $accessToken->getRefreshToken() . "\n";
                // echo $accessToken->getExpires() . "\n";
                // echo ($accessToken->hasExpired()
                //         ? 'expired'
                //         : 'not expired') . "\n";

                $this->setAccessToken($accessToken);

                // // Using the access token, we may look up details about the
                // // resource owner.
                // $resourceOwner = $provider->getResourceOwner($accessToken);
                //
                // var_export($resourceOwner->toArray());
                //
                // // The provider provides a way to get an authenticated API request
                // // for the service, using the access token; it returns an
                // // object conforming
                // // to Psr\Http\Message\RequestInterface.
                // $request = $provider->getAuthenticatedRequest(
                //     'GET',
                //     'http://brentertainment.com/oauth2/lockdin/resource',
                //     $accessToken
                // );

            } catch (IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit($e->getMessage());

            }

        }
    }

    /**
     * Get content through the API or cache in the database
     *
     * @param string  $url      Requested URL
     * @param array   $query    Arguments to the request
     * @param boolean $useCache If true, indicates that the cache will be used
     *
     * @return array
     */
    public function get($url, $query = [], $useCache = true)
    {
        $url = join('/', [rtrim($this->requestUrl, '/'), trim($url, '/')]);

        if (!isset($query['id'])) {
            $query['per_page'] = 100;
            $query['page'] = max(@$query['page'], 1);
        }

        $url .= '?' . http_build_query($query);

        $transitionId = Config::PREFIX . "get_{$url}";

        // $response = get_transient($transitionId);
        $response = $useCache
            ? get_transient($transitionId)
            : null;

        if (!$response) {
            $response = $this->getFromApi($url);
            set_transient(
                $transitionId,
                $response,
                4 * HOUR_IN_SECONDS
            );
        }

        return $response;
    }

    /**
     * Get content through the API
     *
     * @param string $url     Requested URL
     * @param string $context Requested context
     * @param array  $body    Request body
     *
     * @return array
     */
    protected function getFromApi($url, $context = 'GET', $body = [])
    {
        $options['headers']['X-Api-Version'] = $this->apiVersion;
        $options['headers']['content-type'] = 'application/json';
        $options['body'] = json_encode($body);

        $request = $this->provider
            ->getAuthenticatedRequest(
                $context,
                $url,
                $this->getAccessToken(),
                $options
            );
            // ->withHeader('X-Api-Version', $this->apiVersion)
            // ->withHeader('content-type', 'application/json');

        $response = $this->provider->getResponse($request);

        $responseHeaders = $response->getHeaders();
        $responseContents = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException(
                sprintf(
                    "Failed to parse JSON response: %s",
                    json_last_error_msg()
                )
            );
        }

        return [
            'headers' => $responseHeaders,
            'response' => $responseContents,
        ];
    }

    /**
     * Revoke existing access token
     *
     * @return void
     */
    public function revokeAccess()
    {
        $this->setAccessToken(null);
    }

    /**
     * Fetch all items of a path
     *
     * @param string $path  Path to request
     * @param array  $query Query
     *
     * @return array
     */
    static function fetch($path, $query, $useCache = true)
    {
        $tagplus = (new self());

        $query['fields'] = '*';
        $items = [];
        $totalPages = 1;
        $page = 1;

        while ($page <= $totalPages) {
            $query['page'] = $page;
            $response = $tagplus->get($path, $query, $useCache);

            if (is_array(@$response['response'])) {
                $items = array_merge($items, $response['response']);
            }

            if ($page === 1) {
                $totalPages = max($response['headers']['X-Total-Pages'][0], 1);
            }
            $page++;
        }

        return $items;
    }

    public function post($url, $body)
    {
        $url = join('/', [rtrim($this->requestUrl, '/'), trim($url, '/')]);

        return $this->getFromApi($url, 'POST', $body);
    }
}
