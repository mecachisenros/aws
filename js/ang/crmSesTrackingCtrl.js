(function (angular, $, _) {
  angular.module('crmSesTracking').controller('crmSesTrackingCtrl', function($scope, crmApi, $route) {

    $scope.mailingConfigSet = {
      is_active: 0
    };

    var updateConfigSet = function() {
      crmApi('MailingConfigSet', 'create', $scope.mailingConfigSet)
      .then(function (result) {
        CRM.alert(
          ts('Mailing configuration set updated.'),
          ts('Success'), 'success'
        );
      });
    }

    crmApi('MailingConfigSet', 'getsingle', {
      mailing_id: $route.current.locals.selectedMail.id
    }).then(function (result) {
      $scope.mailingConfigSet = result;
    });

    crmApi('MailingConfigSet', 'getoptions', {
      field: 'config_set'
    }).then(function (result) {
      $scope.configSetOptions = result.values;
    });

    $scope.$watch('mailingConfigSet', function(configSet) {
      if (configSet && configSet.is_active) {
        configSet.is_active = parseInt(configSet.is_active);
        $scope.mailing.url_tracking = 0;
        $scope.mailing.open_tracking = 0;
      }
      updateConfigSet();
    }, true);

  });
})(angular, CRM.$, CRM._);
