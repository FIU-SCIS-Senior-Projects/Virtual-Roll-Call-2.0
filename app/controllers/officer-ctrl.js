//CONTROLLER for officer app
officerModule.controller('officerCtrl', ['$scope', 'localStorageService', 'dataService', '$controller', '$routeParams', function($scope, localStorageService, dataService, $controller, $routeParams){

  //TO DO: SAVE AS GLOBALS OR IN SHAREDCTRL
  /***** GLOBALS *****/
  //get name from local storage for user profile customization
  
  
  var fname = localStorageService.get('fname');
  var lname = localStorageService.get('lname');
  var id = localStorageService.get('id');
  $scope.login = localStorageService.get('login');
  $scope.name = fname + ' ' + lname;
  $scope.id = id;
  $scope.password_pattern = '^[a-zA-Z0-9]{8,}$';
  $scope.pattern_descr = 'Must contain at least 8 or more characters. Only alphanumeric characters allowed.';

  /***** SHARED FUNCTIONS *****/
  var sharedCtrl = $controller('sharedCtrl', {$scope: $scope});
  sharedCtrl.redirect($scope.login);

  $scope.getSiteNames = function(){
    sharedCtrl.getSiteNames();
  };

  $scope.getCategories = function(){
    sharedCtrl.getCategories();
  };
  
  $scope.document_confirmation = function(){
      console.log("Confirmation");
  }
 
  $scope.logout = function(){
      sharedCtrl.logout();
  }

  /***********************
 * GET PINNED DOCUMENTS *
 ***********************/
 $scope.viewDocuments = function(){

  $scope.selected_cat = $routeParams.selectedCategory;

  dataService.viewDocuments()
  .then(
    function(data){
      var cat = $routeParams.selectedCategory;

      //initialize an empty array to store results from the database
      var pinned_documents = [];
      var unpinned_documents = [];

      //for each category in the result
      for (var x in data){
    //create an object and set object properties (i.e. documents data)
     if(cat === data[x].cat_name){

      var tmp = new Object();
      tmp.name = data[x].name;
      tmp.upload_name = data[x].upload_name;
      if(data[x].pinned == 1){
        pinned_documents.push(tmp);
      }
      else{
        unpinned_documents.push(tmp);
      }
      }
    }

    //update value in view for use in ng-repeat (to populate)
    $scope.pinned_documents = pinned_documents;
    $scope.unpinned_documents = unpinned_documents;
    
  },
  function(error){
    console.log('Error: ' + error);
  });};

  /***** ALERT FUNCTIONS *****/
  //alert functions (displays accordingly in views)
  $scope.alert = sharedCtrl.alert;

}]);
