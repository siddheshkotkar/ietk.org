<?php

namespace GoDaddy\WordPress\MWC\Core\Auth\Models;

use GoDaddy\WordPress\MWC\Common\Models\AbstractModel;
use GoDaddy\WordPress\MWC\Common\Traits\CanBulkAssignPropertiesTrait;

/**
 * Managed WooCommerce JWT Token.
 */
class ManagedWooCommerceToken extends AbstractModel
{
    use CanBulkAssignPropertiesTrait;
    /** @var string A JWT Access token */
    protected $accessToken = '';

    /** @var string The list of scopes for the JWT separated by space */
    protected $scope = '';

    /** @var string The ID for the token */
    protected $tokenId = '';

    /** @var string The type of token used */
    protected $tokenType = 'BEARER';

    /**
     * Constructor.
     */
    final public function __construct()
    {
        // to prevent overriding the constructor.
    }

    /**
     * Retrieves the access token.
     *
     * @return string The token value.
     */
    public function getAccessToken() : string
    {
        return $this->accessToken;
    }

    /**
     * Retrieves the list of scopes, as a string.
     *
     * @return string List of scopes, separated by a space.
     */
    public function getScope() : string
    {
        return $this->scope;
    }

    /**
     * Retrieves the access token ID.
     *
     * @return string The ID.
     */
    public function getTokenId() : string
    {
        return $this->tokenId;
    }

    /**
     * Retrieves the access token type.
     *
     * @return string The type.
     */
    public function getTokenType() : string
    {
        return $this->tokenType;
    }

    /**
     * Sets the access token.
     *
     * @return $this The token instance.
     */
    public function setAccessToken($token) : ManagedWooCommerceToken
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Sets the scope.
     *
     * @return $this The token instance.
     */
    public function setScope($scope) : ManagedWooCommerceToken
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Sets the token ID.
     *
     * @return $this The token instance.
     */
    public function setTokenId($tokenId) : ManagedWooCommerceToken
    {
        $this->tokenId = $tokenId;

        return $this;
    }

    /**
     * Sets the token type.
     *
     * @return $this The token instance.
     */
    public function setTokenType($tokenType) : ManagedWooCommerceToken
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    /**
     * Creates a new instance of this token, setting values based on the data provided.
     *
     * @param array $data Token values keyed by the property name.
     *
     * @return ManagedWooCommerceToken A new instance of this class, with properties set to the values set in $data.
     */
    public static function seed(array $data = []) : ManagedWooCommerceToken
    {
        return (new static())->setProperties($data);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray() : array
    {
        $data = parent::toArray();
        $data['expiresIn'] = $this->getExpiresIn();

        return $data;
    }

    /**
     * Retrieves the number of seconds before this token expires, based on the expiration date.
     *
     * @return int Seconds before this token expires.
     */
    public function getExpiresIn() : int
    {
        return 0;
        //TODO: IMPLEMENT THIS STUB. MWC-3953
    }
}
