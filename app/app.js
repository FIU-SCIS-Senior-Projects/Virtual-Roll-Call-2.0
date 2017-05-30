var sharedModule = angular.module('shared', ['admin', 'supervisor', 'officer', 'login']);
var loginModule = angular.module('login', ['ngRoute', 'LocalStorageModule', 'shared', 'flow', 'ui.bootstrap']);
var adminModule = angular.module('admin', ['ngRoute', 'LocalStorageModule', 'shared', 'flow', 'ui.bootstrap']);
var supervisorModule = angular.module('supervisor', ['ngRoute', 'LocalStorageModule', 'shared', 'flow', 'ui.bootstrap']);
var officerModule = angular.module('officer', ['ngRoute', 'LocalStorageModule', 'shared', 'flow', 'ui.bootstrap']);

adminModule.config(function($routeProvider){
	$routeProvider
	.when('/categories', {
		templateUrl: 'partials/category-management.html',
		controller: 'adminCtrl'
	})
	.when('/settings', {
		templateUrl: 'partials/site-settings.html',
		controller: 'adminCtrl'
	})
	.when('/user-management', {
		templateUrl: 'partials/user-management.html',
		controller: 'adminCtrl'
	})
	.when('/password', {
		templateUrl: 'partials/change-password.html',
		controller: 'sharedCtrl'
	})
	.when('/reporting', {
		templateUrl: 'partials/reporting.html',
		controller: 'adminCtrl'
	})
	.otherwise({
		redirectTo: '/user-management'
	});
});

supervisorModule.config(function($routeProvider){
	$routeProvider
	.when('/upload', {
		templateUrl: 'partials/manage-documents.html',
		controller: 'supervisorCtrl'
	})
	.when('/reset', {
		templateUrl: 'partials/reset-password.html',
		controller: 'supervisorCtrl'
	})
	.when('/password', {
		templateUrl: 'partials/change-password.html',
		controller: 'sharedCtrl'
	})
	.otherwise({
		redirectTo: '/upload'
	});
});

officerModule.config(function($routeProvider){
	$routeProvider
	.when('/categories', {
		templateUrl: 'partials/view-categories.html',
		controller: 'officerCtrl'
	})
	.when('/documents/:selectedCategory', {
		templateUrl: 'partials/view-documents.html'
	})
	.otherwise({
		redirectTo: '/categories'
	});
});
