<?php

use CRM_Aws_ExtensionUtil as E;

/**
 * AWS SNS BAO controller calss.
 *
 * @since 1.0
 */
class CRM_Aws_Sns_BAO_Sns {

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
      'Name' => [
        'name' => 'Name',
        'title' => E::ts('Topic name'),
        'type' => CRM_Utils_Type::T_STRING,
        'api.aliases' => ['id'],
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'localizable' => 0,
        'description' => E::ts('The topic name.'),
        'actions' => ['create', 'delete', 'get'],
      ],
      'DisplayName' => [
        'name' => 'DisplayName',
        'title' => E::ts('Topic Display Name'),
        'type' => CRM_Utils_Type::T_STRING,
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'localizable' => 0,
        'description' => E::ts('OPTIONAL: The topic display name.'),
        'actions' => ['create', 'get'],
      ],
      'TopicArn' => [
        'name' => 'TopicArn',
        'title' => E::ts('Topic Arn'),
        'type' => CRM_Utils_Type::T_STRING,
        'api.aliases' => ['id'],
        'html' => [
          'type' => 'Text',
          'size' => 50,
        ],
        'localizable' => 0,
        'description' => E::ts('The topic arn.'),
        'actions' => ['get'],
      ],
    ];

    // filter by action
    if (!empty($action)) {
      return array_filter(
        $fields,
        function ($fieldProps) use ($action) {
          return in_array($action, $fieldProps['actions']);
        }
      );
    }

    return $fields;

  }

  /**
   * Retrieves the SNS list of topics
   * with their attributes.
   *
   * @since 1.0
   * @param array $params
   * @return array $result
   */
  public static function get(array $params = []): array {

    if (!empty($params['Name'])) {
      return self::getTopicByName($params);
    } elseif (!empty($params['DisplayName'])) {
      return self::getTopicByDisplayName($params);
    }

    $topics = self::getAllTopics();

    if (empty($topics)) {
      return [];
    }

    if (!empty($params['TopicArn']) && strpos($params['TopicArn'], ':sns:')) {
      $result = $topics[$params['TopicArn']] ?? [];
    } else {
      $result = $topics;
    }

    return $result;

  }

  /**
   * Creates a domain or email identity,
   * if the identity is an email address it
   * will attempt to verify it sending a
   * verification email to the specified address.
   *
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#createtopic
   *
   * @since 1.0
   * @param array $params
   * @return array $result
   */
  public static function create(array $params = []): array {

    $snsClient = awsRegistry()->get('snsClient');

    $params['Name'] = self::buildTopicName($params['Name']);

    $snsParams = [
      'Name' => $params['Name'],
    ];

    if (!empty($params['DisplayName'])) {
      $snsParams['Attributes']['DisplayName'] = $params['DisplayName'];
    }

    $result = $snsClient->createTopic($snsParams);

    $subscriptionArn = $snsClient->subscribe(
      [
        'Endpoint' => self::getWebhookEndpoint(),
        'Protocol' => self::getUrlProtocol(),
        'ReturnSubscriptionArn' => true,
        'TopicArn' => $result->get('TopicArn'),
      ]
    )['SubscriptionArn'];

    $topic = [
      self::get(
        [
          'TopicArn' => $result->get('TopicArn'),
        ]
      ),
    ];

    return $topic;

  }

  /**
   * Deletes an identity from
   * the list of identities.
   *
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#unsubscribe
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-email-2010-12-01.html#deletetopic
   *
   * @since 1.0
   * @param array $params
   * @return array $result
   */
  public static function delete(array $params = []): array {

    $snsClient = awsRegistry()->get('snsClient');

    $topic = civicrm_api3('Sns', 'getsingle', $params);

    // delete subscriptions
    if (!empty($topic['Subscriptions'])) {
      array_map(
        function($subscription) use ($snsClient) {
          $snsClient->unsubscribe(
            $subscription
          );
        },
        $topic['Subscriptions']
      );
    }

    $snsClient->deleteTopic(
      ['TopicArn' => $topic['TopicArn']]
    );

    return [
      $topic['TopicArn'],
    ];

  }

  /**
   * Retrieves all topics with their
   * subscriptions and attributes.
   *
   * NOTE: there doesn't seem to be a way
   * to retrieve topics by name, so we retrieve
   * all of them and filter them later.
   *
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#listtopics
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#gettopicattributes
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#listsubscriptionsbytopic
   * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sns-2010-03-31.html#getsubscriptionattributes
   *
   * @since 1.0
   * @return array $topics
   */
  public function getAllTopics(): array {

    $snsClient = awsRegistry()->get('snsClient');

    $topics = $snsClient->listTopics();

    if (empty($topics['Topics'])) {
      return [];
    }

    $topicsResult = array_column($topics['Topics'], 'TopicArn');

    return array_reduce(
      $topicsResult,
      function ($list, $topicArn) use ($snsClient) {

        $topicParams = ['TopicArn' => $topicArn];

        $topic = array_merge(
          ['TopicArn' => $topicArn],
          $snsClient->getTopicAttributes(
            $topicParams
          )['Attributes'],
          [
            'Subscriptions' => $snsClient->listSubscriptionsByTopic(
              $topicParams
            )['Subscriptions'],
          ]
        );

        if (!empty($topic['Subscriptions'])) {
          $topic['Subscriptions'] = array_reduce(
            $topic['Subscriptions'],
            function ($list, $subscription) use ($snsClient) {

              if ($subscription['SubscriptionArn'] == 'PendingConfirmation') {
                return $list;
              }

              $subscription = array_merge(
                $subscription,
                $snsClient->getSubscriptionAttributes(
                  ['SubscriptionArn' => $subscription['SubscriptionArn']]
                )['Attributes']
              );

              $list[$subscription['SubscriptionArn']] = $subscription;

              return $list;
            },
            []
          );
        }

        $list[$topicArn] = $topic;

        return $list;

      },
      []
    );

  }

  /**
   * Retrieves a topic by the topic name.
   *
   * @since 1.0
   * @param array $params The params array
   * @return array $result The topic
   */
  public static function getTopicByName(array $params): array {

    if (empty($params['Name'])) {
      return [];
    }

    $topics = self::getAllTopics();

    return array_filter(
      $topics,
      function($topicArn) use ($params) {
        return self::getTopicNameFromArn($topicArn) === $params['Name'];
      },
      ARRAY_FILTER_USE_KEY
    );

  }

  /**
   * Retrieves a topic by the display name.
   *
   * @since 1.0
   * @param array $params The params array
   * @return array $result The topic
   */
  public static function getTopicByDisplayName(array $params): array {

    if (empty($params['DisplayName'])) {
      return [];
    }

    $topics = self::getAllTopics();

    return array_filter(
      $topics,
      function($topic) use ($params) {
        return !empty($topic['DisplayName'])
          && $topic['DisplayName'] === $params['DisplayName'];
      }
    );

  }

  /**
   * Retrieves a list of TopicArns
   * options indexed by topicArn and
   * with the topic name as the label.
   *
   * @since 1.0
   * @return array $topicArnOptions
   */
  public function getTopicArnOptions():array {
    $topics = self::getAllTopics();
    return array_reduce(
      $topics,
      function($list, $topic) {
        $list[$topic['TopicArn']] = self::getPropFromArn($topic['TopicArn'], 'topicName');
        return $list;
      },
      []
    );
  }

  /**
   * Constructs the topic name for bounces.
   *
   * A given name like andrei@ExaMpLe.org
   * will be converted to:
   * 'andrei_at_ExaMpLe_org'.
   *
   * @since 1.0
   * @param string $name The topic name
   * @param string $prefix A string to prefix/prepend to the topic name
   * @return string $name The altered topic name
   */
  public static function buildTopicName(string $name, string $prefix = ''): string {
    $name = empty($prefix) ? $name : "{$prefix}_{$name}";
    return preg_replace('/[^A-Za-z0-9]+/', '_', str_replace('@', '_at_', trim($name)));
  }

  /**
   * Retrieves the topic name
   * for a given topic arn.
   *
   * @since 1.0
   * @param string $topicArn The topic arn
   * @return string $topicName The topic name
   */
  public static function getTopicNameFromArn(string $topicArn): string {
    return self::getPropFromArn($topicArn, 'topicName');
  }

  /**
   * Retrives a property name for a given arn.
   *
   * @since 1.0
   * @param string $topicArn The topic arn
   * @param string $prop The propery i.e. 'arn|aws|service|region|clientId|topicName'
   * @return string $propValue
   */
  public static function getPropFromArn(string $topicArn, string $prop): string {
    list($arn, $aws, $service, $region, $clienId, $topicName) = explode(':', $topicArn);
    return $$prop;
  }

  /**
   * Retrieves the SNS webhook endpoint.
   *
   * @since 1.0
   * @return string $endpoint
   */
  public static function getWebhookEndpoint(): string {
    return CRM_Utils_System::url('civicrm/aws-sns/webhook', null, true, null, false, true);
  }

  /**
   * Retrieves the url
   * protocol for this site.
   *
   * @since 1.0
   * @return string $protocol http|https
   */
  public function getUrlProtocol(): string {
    return parse_url(self::getWebhookEndpoint(), PHP_URL_SCHEME);
  }

}
