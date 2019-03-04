module.exports = {
	// ...
	dev: {
		proxyTable: {
			// proxy all requests starting with /api to jsonplaceholder
			'/api': {
				target: 'http://electro',
				changeOrigin: true
			}
		}
	}
}