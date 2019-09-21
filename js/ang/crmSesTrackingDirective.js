(function (angular, $, _) {
  angular.module('crmSesTracking').directive('crmSesTracking', function () {
    return {
      templateUrl: CRM.resourceUrls['aws'] + '/js/ang/crmSesTracking.html',
      controller: 'crmSesTrackingCtrl',
    };
  });
})(angular, CRM.$, CRM._);
