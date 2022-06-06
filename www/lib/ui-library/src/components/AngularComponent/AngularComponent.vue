<template>
	<div></div>
</template>

<script>
export default {
	name: "AngularComponent",
	props: ['component'],
	mounted () {
		const el = angular.element(this.$el);
		const componentTemplate = this.component.template;
		const componentCtrl = this.component.$ctrl;

		el.injector().invoke(['$compile', '$rootScope', function($compile, $rootScope) {
			const scope = angular.extend($rootScope.$new(), {$ctrl: componentCtrl});
			el.append($compile(componentTemplate)(scope));
		}]);
	}
};
</script>