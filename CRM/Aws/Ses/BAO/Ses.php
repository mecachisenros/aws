<?php

use CRM_Aws_ExtensionUtil as E;
use Aws\Exception\AwsException;

/**
 * AWS SES BAO controller calss.
 *
 * @since 1.0
 */
class CRM_Aws_Ses_BAO_Ses {

  /**
   * Retrieves the fields for this
   * entity for a given action.
   *
   * @since 1.0
   * @param string $action The API action
   * @return array $fields
   */
  public static function getFields(string $action = ''): array {

    $fields = [
      'Identity' => [
        'name' => 'Identity',
        'title' => E::ts('Identity'),
        'type' => CRM_Utils_Type::T_STRING,
        'api.aliases' => ['id'],
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'localizable' => 0,
        'description' => E::ts('The email address or domain identity.'),
        'actions' => ['create', 'get', 'delete'],
      ],
      'CreateTopic' => [
        'name' => 'CreateTopic',
        'title' => E::ts('Create SNS Topic'),
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'html' => [
          'type' => 'CheckBox',
        ],
        'localizable' => 0,
        'description' => E::ts('Wheather to create a SNS Topic.'),
        'actions' => ['create'],
      ],
      'TopicName' => [
        'name' => 'TopicName',
        'title' => E::ts('Topic Name'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'localizable' => 0,
        'description' => E::ts('OPTIONAL: The SNS Topic name, leave empty to create it from the identity.'),
        'actions' => ['create', 'get'],
      ],
      'TopicDisplayName' => [
        'name' => 'TopicDisplayName',
        'title' => E::ts('Topic Display Name'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'localizable' => 0,
        'description' => E::ts('OPTIONAL: The SNS Topic display name, leave empty to create it from the identity.'),
        'actions' => ['create', 'get'],
      ],
      'BounceTopic' => [
        'name' => 'BounceTopic',
        'title' => E::ts('Bounce Topic'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Select',
        ],
        'options' => CRM_Aws_Sns_BAO_Sns::getTopicArnOptions(),
        'api.return' => 1,
        'localizable' => 0,
        'description' => E::ts('The SNS bounce topic.'),
        'actions' => ['create', 'get'],
      ],
      'ComplaintTopic' => [
        'name' => 'ComplaintTopic',
        'title' => E::ts('Complaint Topic'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Select',
        ],
        'options' => CRM_Aws_Sns_BAO_Sns::getTopicArnOptions(),
        'api.return' => 1,
        'localizable' => 0,
        'description' => E::ts('The SNS complaint topic.'),
        'actions' => ['create', 'get'],
      ],
      'DeliveryTopic' => [
        'name' => 'DeliveryTopic',
        'title' => E::ts('Delivery Topic'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Select',
        ],
        'options' => CRM_Aws_Sns_BAO_Sns::getTopicArnOptions(),
        'api.return' => 1,
        'localizable' => 0,
        'description' => E::ts('The SNS delivery topic.'),
        'actions' => ['create', 'get'],
      ],
      'Type' => [
        'name' => 'Type',
        'title' => E::ts('Identity type'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Select',
        ],
        'options' => self::getIdentityTypes(),
        'pseudoconstant' => [
          'callback' => 'CRM_Aws_Ses_BAO_Ses::getIdentityTypes',
        ],
        'localizable' => 0,
        'description' => E::ts('OPTIONAL: The identity type "email" or "domain".'),
        'actions' => ['get'],
      ],
      'VerificationStatus' => [
        'name' => 'VerificationStatus',
        'title' => E::ts('Verification Status'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'api.return' => 1,
        'api.filter' => 0,
        'localizable' => 0,
        'description' => E::ts('The identity verification status.'),
        'actions' => ['get'],
      ],
      'VerificationToken' => [
        'name' => 'VerificationToken',
        'title' => E::ts('Verification Token'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'api.return' => 1,
        'api.filter' => 0,
        'localizable' => 0,
        'description' => E::ts('The identity verification token.'),
        'actions' => ['get'],
      ],
      'ForwardingEnabled' => [
        'name' => 'ForwardingEnabled',
        'title' => E::ts('Email Feedback Forwarding'),
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'html' => [
          'type' => 'CheckBox',
        ],
        'api.default' => 1,
        'localizable' => 0,
        'description' => E::ts('Email Feedback Forwarding.'),
        'actions' => ['create', 'get'],
      ],
      'HeadersInBounceNotificationsEnabled' => [
        'name' => 'HeadersInBounceNotificationsEnabled',
        'title' => E::ts('Bounce original headers'),
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'html' => [
          'type' => 'CheckBox',
        ],
        'api.default' => 1,
        'localizable' => 0,
        'description' => E::ts('Wheather to include the original headers in Bounce notifications.'),
        'actions' => ['create', 'get'],
      ],
      'HeadersInComplaintNotificationsEnabled' => [
        'name' => 'HeadersInComplaintNotificationsEnabled',
        'title' => E::ts('Complaint original headers'),
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'html' => [
          'type' => 'CheckBox',
        ],
        'api.default' => 1,
        'localizable' => 0,
        'description' => E::ts('Wheather to include the original headers in Complaint notifications.'),
        'actions' => ['create', 'get'],
      ],
      'HeadersInDeliveryNotificationsEnabled' => [
        'name' => 'HeadersInDeliveryNotificationsEnabled',
        'title' => E::ts('Delivery original headers'),
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'html' => [
          'type' => 'CheckBox',
        ],
        'api.default' => 0,
        'localizable' => 0,
        'description' => E::ts('Wheather to include the original headers in Delivery notifications.'),
        'actions' => ['create', 'get'],
      ],
      'DkimEnabled' => [
        'name' => 'DkimEnabled',
        'title' => E::ts('Dkim Signing'),
        'type' => CRM_Utils_Type::T_BOOLEAN,
        'html' => [
          'type' => 'CheckBox',
        ],
        'api.default' => 1,
        'localizable' => 0,
        'description' => E::ts('Enable Dkim signing.'),
        'actions' => ['create', 'get'],
      ],
      'MailFromDomain' => [
        'name' => 'MailFromDomain',
        'title' => E::ts('Mail From Domain'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'api.return' => 1,
        'api.filter' => 0,
        'localizable' => 0,
        'description' => E::ts('The identity mail from domain.'),
        'actions' => ['create', 'get'],
      ],
    ];

    // filter by action
    if (!empty($action)) {
      return array_filter(
        $fields,
        function($fieldProps) use ($action) {
          return in_array($action, $fieldProps['actions']);
        }
      );
    }

    return $fields;

  }

  /**
   * Retrieves the SES list of identities
   * with their attributes, including verification,
   * notification, dkim and mail from attributes.
   *
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentityverificationattributes
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentitynotificationattributes
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentitydkimattributes
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#getidentitymailfromdomainattributes
   *
   * @since 1.0
   * @param array $params
   * @return array $result
   */
  public static function getIdentities(array $params = []): array {

    $sesClient = awsRegistry()->get('sesClient');

    if (empty($params['Identity'])) {
      $params['Identity'] = $sesClient->listIdentities()['Identities'];
    }

    if (empty($params['Identity'])) {
      return [];
    }

    $identities = [
      'Identities' => is_array($params['Identity'])
        ? $params['Identity']
        : [$params['Identity']],
    ];

    $attributes = [
      $sesClient->getIdentityVerificationAttributes(
        $identities
      )['VerificationAttributes'],
      $sesClient->getIdentityNotificationAttributes(
        $identities
      )['NotificationAttributes'],
      $sesClient->getIdentityDkimAttributes(
        $identities
      )['DkimAttributes'],
      $sesClient->getIdentityMailFromDomainAttributes(
        $identities
      )['MailFromDomainAttributes'],
    ];

    // result
    return array_reduce(
      $identities['Identities'],
      function ($list, $identityName) use ($attributes, $params) {

        $identity = array_column(
          $attributes,
          $identityName
        );

        // flattened attributes
        $identity = array_merge(...$identity);

        $identity['Type'] = self::getIdentityType($identityName);

        if (!empty($identity['VerificationToken'])) {
          $identity['VerificationToken'] = self::buildTXTVerificationRecord(
            $identity['VerificationToken'],
            $identityName
          );
        }

        if (!empty($identity['DkimTokens'])) {
          $identity['DkimTokens'] = self::buildDkimDNSRecords(
            $identity['DkimTokens'],
            $identityName
          );
        }

        $topics = array_reduce(
          $identity,
          function($list, $value) {
            if (is_string($value) && strpos($value, 'aws:sns')) {
              $list[$value] = civicrm_api3('Sns', 'get', ['TopicArn' => $value])['values'];
            }
            return $list;
          },
          []
        );

        if (!empty($topics)) {
          $identity['Topics'] = $topics;
        }

        // handle return filter
        if (!empty($params['return'])) {
          // return filter is a comma separatted string
          $return = explode(',', $params['return']);
          // filter based on 'return' filter
          $identity = array_filter(
            $identity,
            function($fieldName) use ($return) {
              return in_array($fieldName, $return);
            },
            ARRAY_FILTER_USE_KEY
          );
        }

        // ensure we always return 'identity' and 'id'
        // props for consistency wity Civi and SES
        $list[$identityName] = array_merge(
          ['Identity' => $identityName],
          $identity
        );

        return $list;

      },
      []
    );

  }

  /**
   * Creates a domain or email identity,
   * if the identity is an email address it
   * will attempt to verify it sending a
   * verification email to the specified address.
   *
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#verifyemailaddress
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#verifydomainidentity
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentityheadersinnotificationsenabled
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentityfeedbackforwardingenabled
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentitydkimenabled
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#setidentitymailfromdomain
   *
   * @since 1.0
   * @param array $params
   * @return array $result
   */
  public static function createIdentity(array $params = []): array {

    $sesClient = awsRegistry()->get('sesClient');
    $identityType = self::getIdentityType($params['Identity']);

    switch ($identityType) {
      case 'email':
        $sesClient->verifyEmailAddress(
          ['EmailAddress' => $params['Identity']]
        );
        break;

      case 'domain':
        $result = $sesClient->verifyDomainIdentity(
          ['Domain' => $params['Identity']]
        );
        break;
    }

    // create topic
    if (!empty($params['CreateTopic'])) {
      $topic = civicrm_api3('Sns', 'create', ['Name' => $params['Identity']]);
      array_map(
        function ($type) use ($params, $sesClient, $topic) {
          $sesClient->setIdentityNotificationTopic(
            [
              'Identity' => $params['Identity'],
              'NotificationType' => $type,
              'SnsTopic' => $topic['values'][0]['TopicArn'],
            ]
          );
        },
        ['Bounce', 'Complaint']
      );
    }

    // set forwarding
    if (isset($params['ForwardingEnabled'])) {
      $sesClient->setIdentityFeedbackForwardingEnabled(
        [
          'Identity' => $params['Identity'],
          'ForwardingEnabled' => (bool) $params['ForwardingEnabled'],
        ]
      );
    }

    // set dkim
    if (isset($params['DkimEnabled'])) {
      // we must initiate dkim before enabling/disabling
      $sesClient->verifyDomainDkim(
        [
          'Domain' => self::getDomainFromIdentity($params['Identity']),
        ]
      );
      $sesClient->setIdentityDkimEnabled(
        [
          'Identity' => $params['Identity'],
          'DkimEnabled' => (bool) $params['DkimEnabled'],
        ]
      );
    }

    // set mail from
    if (!empty($params['MailFromDomain'])) {
      $sesClient->setIdentityMailFromDomain(
        $params
      );
    }

    array_map(
      function ($field, $value) use ($params, $sesClient) {

        // set notification topics
        if (
          strpos($field, 'Topic')
          && strpos($value, 'aws:sns')
          && $field != 'Topics'
        ) {
          $sesClient->setIdentityNotificationTopic(
            [
              'Identity' => $params['Identity'],
              'NotificationType' => str_replace('Topic', '', $field),
              'SnsTopic' => $value,
            ]
          );
        }

        // set headers notificationn
        if (strpos($field, 'NotificationsEnabled')) {
          $sesClient->setIdentityHeadersInNotificationsEnabled(
            [
              'Identity' => $params['Identity'],
              'Enabled' => (bool) $value,
              'NotificationType' => str_replace(
                ['HeadersIn', 'NotificationsEnabled'],
                ['', ''],
                $field
              ),
            ]
          );
        }

      },
      array_keys($params),
      $params
    );

    // return created identity
    $result = civicrm_api3('Ses', 'getsingle', $params);

    return $result;

  }

  /**
   * Deletes an identity from
   * the list of identities.
   *
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#deleteidentity
   *
   * @since 1.0
   * @param array $params
   * @return array $result
   */
  public static function deleteIdentity(array $params = []): array {

    $sesClient = awsRegistry()->get('sesClient');

    $result = $sesClient->deleteIdentity(
      ['Identity' => $params['Identity']]
    );

    return $result->toArray();

  }

  /**
   * Retrieves the SES action links.
   *
   * @since 1.0
   * @return string $links
   */
  public static function getActionLinks(string $identityName): string {

    $links = [
      CRM_Core_Action::UPDATE => [
        'name' => E::ts('Edit'),
        'url' => 'civicrm/aws-ses/identity/edit',
        'qs' => 'id=%%id%%',
        'title' => E::ts('Edit'),
        'class' => 'crm-popup',
      ],
      CRM_Core_Action::DELETE => [
        'name' => E::ts('Delete'),
        'url' => 'civicrm/aws-ses/identity/delete',
        'qs' => 'id=%%id%%',
        'title' => E::ts('Delete'),
        'class' => 'crm-popup',
      ],
    ];

    return CRM_Core_Action::formLink(
      $links,
      null,
      ['id' => $identityName]
    );

  }

  /**
   * Retrieves the identity type for a
   * given identity.
   *
   * @since 1.0
   * @param string $identity The email address or domain identity
   * @return string $identityType email|domain
   */
  public static function getIdentityType(string $identity): string {
    return strpos($identity, '@') ? 'email' : 'domain';
  }

  /**
   * Retrieves the SES identity types.
   *
   * @since 1.0
   * @return array $identityTypes
   */
  public static function getIdentityTypes(): array {
    return [
      'domain' => E::ts('Domain'),
      'email' => E::ts('Email address'),
    ];
  }

  public function getNotificationHeadersOptions(): array {
    return [
      'HeadersInBounceNotificationsEnabled' => E::ts('Bounce'),
      'HeadersInComplaintNotificationsEnabled' => E::ts('Complaint'),
      'HeadersInDeliveryNotificationsEnabled' => E::ts('Delivery'),
    ];
  }

  /**
   * Retrieves the domain for
   * a given identity.
   *
   * @since 1.0
   * @param string $identity
   * @return string $domain
   */
  public static function getDomainFromIdentity(string $identity): string {
    return ($domain = strstr($identity, '@')) !== false
      ? substr($domain, 1)
      : $identity;
  }

  /**
   * Builds the Dkim CNAME records
   * for a given tokens array.
   *
   * @since 1.0
   * @param array $dkimTokens The dkim tokens
   * @param string $identity The emails adress or domain identity
   * @return array $cnameRecords
   */
  public static function buildDkimDNSRecords(array $dkimTokens, string $identity): array {
    $domain = self::getDomainFromIdentity($identity);
    return array_map(
      function ($token) use ($domain) {
        return "CNAME: {$token}._domainkey.{$domain} => {$token}.dkim.amazonses.com";
      },
      $dkimTokens
    );
  }

  /**
   * Builds the TXT DNS records
   * for a given tokens array.
   *
   * @since 1.0
   * @param string $verficationToken The TXT verification token
   * @param string $identity The emails adress or domain identity
   * @return array $txtRecord
   */
  public static function buildTXTVerificationRecord(string $verficationToken, string $identity): string {
    $domain = self::getDomainFromIdentity($identity);
    return "TXT: _amazonses.{$domain} => {$verficationToken}";
  }

  /**
   * Retreives the configuration sets.
   *
   * @since 1.0
   * @return array $configSets
   */
  public static function getConfigurationSets(): array {

    $sesClient = awsRegistry()->get('sesClient');

    $sets = $sesClient->listConfigurationSets()['ConfigurationSets'];

    if (empty($sets)) {
      return [];
    }

    return array_column($sets, 'Name', 'Name');

    // return array_reduce(
    //   array_column($sets, 'Name'),
    //   function ($list, $setName) use ($sesClient) {
    //     $list[$setName] = $sesClient->describeConfigurationSet(
    //       [
    //         'ConfigurationSetName' => $setName,
    //         'ConfigurationSetAttributeNames' => [
    //           'deliveryOptions',
    //           'eventDestinations',
    //           'reputationOptions',
    //           'trackingOptions',
    //         ],
    //       ]
    //     )->toArray();
    //     return $list;
    //   },
    //   []
    // );

  }

}
