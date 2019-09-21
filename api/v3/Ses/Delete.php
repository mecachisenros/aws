<?php

/**
 * Ses.Delete API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_ses_Delete_spec(&$spec) {
  $spec = array_merge($spec, CRM_Aws_Ses_BAO_Ses::getFields('delete'));
  $spec['Identity']['api.required'] = 1;
}

/**
 * Ses.Delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_ses_Delete($params) {
  return civicrm_api3_create_success(
    CRM_Aws_Ses_BAO_Ses::deleteIdentity($params),
    $params,
    'Ses',
    'delete'
  );
}
