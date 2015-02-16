NetCommonsApp.controller('Bbses',
  function($scope, NetCommonsBase, NetCommonsWysiwyg,
           NetCommonsTab, NetCommonsUser) {

      $scope.tab = NetCommonsTab.new();

      $scope.user = NetCommonsUser.new();

      $scope.tinymce = NetCommonsWysiwyg.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(data) {
        $scope.bbses = angular.copy(data);
        console.debug($scope.bbses);
        //編集データセット
        //$scope.edit.data = angular.copy($scope.bbses.bbsPosts);
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
      };

      /**
       * edit _method
       *
       * @type {Object.<string>}
       */
//      $scope.edit = {
//        _method: 'POST',
//        data: {}
//      };

    });

NetCommonsApp.filter('Bbses.filter', function () {
        return function (text, len, content) {
          end = '…';
          if (content) {
            text = angular.element(text).text();
            end = "…";
          }
          if (len === undefined) {
            // デフォルトは10文字
            len = 10;
          }
          if(text !== undefined) {
            if(text.length > len) {
              return text.substring(0, len - 1) + end;
            }
            else {
              return text;
            }
          }
        };
    });

NetCommonsApp.controller('BbsEdit',
  function($scope, NetCommonsBase, NetCommonsTab) {

      $scope.tab = NetCommonsTab.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(data) {
        $scope.bbses = angular.copy(data);
        console.debug($scope.bbses);
        //編集データセット
        //$scope.edit.data = angular.copy($scope.bbses.bbsPosts);
      };

      $scope.save = function() {
        console.debug(2);
      };

    });

NetCommonsApp.controller('BbsFrameSettings',
  function($scope, NetCommonsBase, NetCommonsTab) {

      $scope.tab = NetCommonsTab.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(data) {
        $scope.bbses = angular.copy(data);
        console.debug($scope.bbses);
        //編集データセット
        //$scope.edit.data = angular.copy($scope.bbses.bbsPosts);
      };

      $scope.save = function() {
        console.debug(3);
      };

    });

NetCommonsApp.controller('BbsAuthoritySettings',
  function($scope, NetCommonsBase, NetCommonsTab) {

      $scope.tab = NetCommonsTab.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(data) {
        $scope.bbses = angular.copy(data);
        console.debug($scope.bbses);
        //編集データセット
        //$scope.edit.data = angular.copy($scope.bbses.bbsPosts);
      };

      $scope.save = function() {
        console.debug(4);
      };

    });