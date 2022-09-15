module.exports = {
	computed: {
		theme() {
			if (this.vuex_theme.value) {				
				let theme = this.vuex_theme.value;
				let ladder = theme.ladder || 10;
				let number = theme.number || 9;
				let border = theme.border || 5;
				let colorArr = this.$u.colorGradient(theme.bgColor, theme.color, ladder);								
				return Object.assign({
					lightColor:colorArr[number] || '#f5f5f5',
					faBorderColor:colorArr[border] || '#f5f5f5'
				},theme);
			}
			return {
				bgColor: "#0301c5",				
				color: "#ffffff",			
				lightColor:'#f5f5f5',
				faBorderColor:'#f5f5f5'
			};
		},	
		btnBgColor(param, type) {
			return (param, type) => {
				if(typeof type == 'undefined'){
					return param ? this.theme.bgColor : this.theme.lightColor;
				}
				return param == type ? this.theme.bgColor : this.theme.lightColor;
			};
		},
		btnColor(param, type) {
			return (param, type) => {
				if(typeof type == 'undefined'){
					return param? this.theme.color : this.theme.bgColor;
				}
				return param == type ? this.theme.color : this.theme.bgColor;
			};
		}
	},
}
