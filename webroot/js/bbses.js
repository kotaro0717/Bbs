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
        $scope.bbses = angular.copy(data);
        console.log($scope.bbses);
      };

      $scope.save = function(status) {
        console.debug(2);
      };

      /**
       * @param {number} frameId
       * @return {void}
       */
      $scope.delete = function(postId) {
        var message = 'Do you want to delete the this posts?<br />' +
                      '(It should use defined language.)';
//        dialogs.confirm(undefined, message)
//          .result.then(
//            function(yes) {
//              $http.delete('/bbses/bbsPosts/' + postId.toString())
//                .success(function(data, status, headers, config) {
//                    //$scope.deleted = true;
//                  })
//                .error(function(data, status, headers, config) {
//                    alert(status);  // It should be error code
//                  });
//            });
      }

    });
