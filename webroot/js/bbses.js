/**
 * @fileoverview Bbses Javascript
 * @author kotaro.hokada@gmail.com (Kotaro Hokada)
 */


/**
 * Bbses Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, $modalStack)} Controller
 */
NetCommonsApp.controller('Bbses',
  function($scope, NetCommonsBase, NetCommonsWysiwyg,
           NetCommonsTab, NetCommonsUser) {

      $scope.tab = NetCommonsTab.new();

      $scope.user = NetCommonsUser.new();

      $scope.tinymce = NetCommonsWysiwyg.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(data) {
        $scope.bbses = angular.copy(data.bbses);
      };

      $scope.save = function(status) {
        console.debug(2);
      };

    });
