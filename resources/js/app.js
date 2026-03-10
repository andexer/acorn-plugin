(() => {
	class App {
		init() {
			this.saludar();
		}
		saludar() {
			console.log('hola desde acorn');
		}
	}
	new App().init();
})();