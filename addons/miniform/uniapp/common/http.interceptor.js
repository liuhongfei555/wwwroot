//免登录接口
let noLoginUrl = [	
	'/addons/miniform/common/init',
	'/addons/miniform/common/captcha',
	'/addons/miniform/login/login',
	'/addons/miniform/login/mobilelogin',
	'/addons/miniform/login/register',
	'/addons/miniform/login/resetpwd',
	'/addons/miniform/login/wxLogin',
	'/addons/miniform/login/appLogin',
	'/addons/miniform/login/getWechatMobile',
	'/addons/third/api/getAuthUrl',
	'/addons/third/api/callback',
	'/addons/third/api/account',
	'/addons/miniform/index/project',
	'/addons/miniform/index/projectInfo',
	'/addons/miniform/ajax/selectpage',
	'/addons/miniform/index/projectUser',
	'/addons/miniform/ems/send',
	'/addons/miniform/sms/send',
	'/addons/miniform/user/getSigned',
	'/addons/miniform/login/aliLogin',
	'/addons/miniform/index/diyform',
	'/addons/miniform/index/postForm',
	'/addons/miniform/common/getWxCode',
];

//设置session_id
const getSessionId = function(vm) {
	let session = vm.$util.getDb('session');	
	if (!session) {
		let guid = vm.$u.guid();		
		vm.$util.setDb('session', guid);
		return guid;
	}
	return session;
}

// 这里的vm，就是我们在vue文件里面的this，所以我们能在这里获取vuex的变量，比如存放在里面的token
// 同时，我们也可以在此使用getApp().globalData，如果你把token放在getApp().globalData的话，也是可以使用的
const install = (Vue, vm) => {	
	// let url = 'http://www.fa.com';
	let url = 'http://localhost:1086';
	// #ifdef H5
		if(typeof window.fastUrl !== 'undefined'){
			url = window.fastUrl;
		}		
	// #endif
	Vue.prototype.$u.http.setConfig({
		baseUrl: url,
		header: {
			'content-type': 'application/json'
		},
		originalData: true
	});
	// 请求拦截，配置Token等参数
	Vue.prototype.$u.http.interceptor.request = (config) => {
		//在需要登录的接口，请求前判断token 是否存在,不存在则到登录
		console.log(noLoginUrl.includes(config.url),config.url);
		if (!noLoginUrl.includes(config.url) && !vm.vuex_token) {
			vm.$u.route('/pages/login/mobilelogin');
			return false;
		}
		config.header.token = vm.vuex_token;
		//设置session_id
		config.header.sid = getSessionId(vm);
		config.header.uid = vm.vuex_user.id || 0;
		const res = uni.getSystemInfoSync();
		config.header.platform = res.platform || '';
		config.header.model = res.model || '';
		config.header.brand = res.brand || '';
		config.header['x-requested-with'] = 'xmlhttprequest';		
		if (config.method == 'POST') {
			config.data['__token__'] = vm.vuex__token__;
		}		
		return config;
	}
	// 响应拦截，判断状态码是否通过
	Vue.prototype.$u.http.interceptor.response = (res) => {			
		//返回__token__,设置
		if (res.header && res.header.__token__) {
			vm.$u.vuex('vuex__token__', res.header.__token__);
		}		
		let result = res.data || {};		
		if(result.data && result.data.__token__){
			vm.$u.vuex('vuex__token__', result.data.__token__);
		}
		switch (result.code) {
			case 1:
			case 0:
				return result;
				break;
			case 401:
				//需要登录的接口，当token 过期时，到登录页面
				vm.$u.vuex('vuex_token', '');
				vm.$u.route('/pages/login/mobilelogin');
				return result;
				break;
			case 403: //没有权限访问
				uni.showToast({
					icon: "none",
					title: result.msg
				})
				return result;
				break;
			default:
				if (res.statusCode == 200) {
					return res.data;
				} else {
					console.error(res)
					vm.$u.toast('网络请求错误！');
					return false;
				}
		}

	}
}

export default {
	install
}
