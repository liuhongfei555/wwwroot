const upload = async function(vm, {
		// #ifdef APP-PLUS || H5
		files,
		// #endif
		// #ifdef H5
		file,
		// #endif
		// #ifdef MP-ALIPAY
		fileType,
		// #endif
		filePath,
		name,
		formData
	}) {
	return new Promise((resolve, reject) => {
		uni.showLoading({
			mask: true,
			title: '上传中'
		});
		let data = {
			url: vm.vuex_config.upload.uploadurl,
			header: {
				token: vm.vuex_token || '',
				uid: vm.vuex_user.id || 0
			},
			name: 'file',
			complete: function() {
				uni.hideLoading();
			},
			success: uploadFileRes => {
				try {
					var res = uploadFileRes.data;
					if (vm.$u.test.jsonString(res)) {
						resolve(JSON.parse(res))
					}
					if (vm.$u.test.object(res)) {
						resolve(res)
					}
				} catch (e) {
					reject(uploadFileRes.data);
				}
			},
			fail: (e) => {
				reject(e);
			}
		};
		// #ifdef H5
		//有文件对象，一般是H5
		if(file){
			data.file = file;
		}
		// #endif
		//文件路径
		if(filePath){
			data.filePath = filePath;
		}
		let isObj = vm.$u.test.object(vm.vuex_config.upload.multipart);
		if (isObj && formData) {
			data.formData = Object.assign(formData,vm.vuex_config.upload.multipart);
		}else if(isObj){
			data.formData = vm.vuex_config.upload.multipart;
		}else if(formData){
			data.formData = formData;
		}
		uni.uploadFile(data);
	})

}

const install = (Vue, vm) => {

	vm.$api.config 			  = async (params = {}) => await vm.$u.get('/addons/miniform/common/init', params);
	vm.$api.captcha 		  = async (params = {}) => await vm.$u.get('/addons/miniform/common/captcha', params);
	vm.$api.getWxCode 		  = async (params = {}) => await vm.$u.post('/addons/miniform/common/getWxCode', params);
	vm.$api.goUpload 		  = async (params = {}) => await upload(vm, params);
	vm.$api.getSigned 		  = async (params = {}) => await vm.$u.post('/addons/miniform/user/getSigned',params);
	vm.$api.getUserIndex 	  = async (params = {}) => await vm.$u.get('/addons/miniform/user/index', params);
	vm.$api.getUserProfile 	  = async (params = {}) => await vm.$u.post('/addons/miniform/user/profile', params);
	vm.$api.getUserBind 	  = async (params = {}) => await vm.$u.post('/addons/miniform/user/bind', params);
	vm.$api.goUserLogout 	  = async (params = {}) => await vm.$u.get('/addons/miniform/user/logout', params);
	vm.$api.goUserAvatar 	  = async (params = {}) => await vm.$u.post('/addons/miniform/user/avatar', params);

	vm.$api.getEmsSend 		  = async (params = {}) => await vm.$u.post('/addons/miniform/ems/send', params);
	vm.$api.getSmsSend 		  = async (params = {}) => await vm.$u.post('/addons/miniform/sms/send', params);
	vm.$api.goLogin 		  = async (params = {}) => await vm.$u.post('/addons/miniform/login/login', params);
	vm.$api.mobilelogin 	  = async (params = {}) => await vm.$u.post('/addons/miniform/login/mobilelogin', params);
	vm.$api.goRegister 		  = async (params = {}) => await vm.$u.post('/addons/miniform/login/register', params);
	vm.$api.goResetpwd 		  = async (params = {}) => await vm.$u.post('/addons/miniform/login/resetpwd', params);
	vm.$api.gowxLogin 		  = async (params = {}) => await vm.$u.post('/addons/miniform/login/wxLogin', params);
	vm.$api.goAppLogin 		  = async (params = {}) => await vm.$u.post('/addons/miniform/login/appLogin', params);
	vm.$api.getWechatMobile   = async (params = {}) => await vm.$u.post('/addons/miniform/login/getWechatMobile', params);
	vm.$api.getAuthUrl 	  	  = async (params = {}) => await vm.$u.get('/addons/third/api/getAuthUrl', params);
	vm.$api.goAuthCallback 	  = async (params = {}) => await vm.$u.post('/addons/third/api/callback', params);
	vm.$api.goThirdAccount 	  = async (params = {}) => await vm.$u.post('/addons/third/api/account', params);


	vm.$api.signinConfig 	  = async (params = {}) => await vm.$u.get('/addons/signin/api.index/index',params);
	vm.$api.monthSign 	  	  = async (params = {}) => await vm.$u.get('/addons/signin/api.index/monthSign',params);
	vm.$api.dosign 	 	      = async (params = {}) => await vm.$u.post('/addons/signin/api.index/dosign',params);
	vm.$api.fillup 	  		  = async (params = {}) => await vm.$u.post('/addons/signin/api.index/fillup',params);
	vm.$api.signRank 	 	  = async (params = {}) => await vm.$u.get('/addons/signin/api.index/rank',params);
	vm.$api.signLog 	  	  = async (params = {}) => await vm.$u.get('/addons/signin/api.index/signLog',params);


	vm.$api.project 	  	  = async (params = {}) => await vm.$u.get('/addons/miniform/index/project',params);
	vm.$api.projectInfo 	  = async (params = {}) => await vm.$u.get('/addons/miniform/index/projectInfo',params);
	vm.$api.diyform 	  	  = async (params = {}) => await vm.$u.get('/addons/miniform/index/diyform',params);
	vm.$api.postForm 	  	  = async (params = {}) => await vm.$u.post('/addons/miniform/index/postForm',params);
	vm.$api.selectpage 		  = async (params = {}) => await vm.$u.get('/addons/miniform/ajax/selectpage',params);
	vm.$api.subscribe 		  = async (params = {}) => await vm.$u.post('/addons/miniform/ajax/subscribe',params);
	vm.$api.order 		  	  = async (params = {}) => await vm.$u.post('/addons/miniform/pay/order',params);
	vm.$api.payment 		  = async (params = {}) => await vm.$u.post('/addons/miniform/pay/pay',params);
	vm.$api.myProject 		  = async (params = {}) => await vm.$u.get('/addons/miniform/index/myProject',params);
	vm.$api.logDetail 		  = async (params = {}) => await vm.$u.get('/addons/miniform/index/logDetail',params);
	vm.$api.cancel 		 	  = async (params = {}) => await vm.$u.post('/addons/miniform/index/cancel',params);
	vm.$api.projectUser 	  = async (params = {}) => await vm.$u.get('/addons/miniform/index/projectUser',params);
	vm.$api.toSignin 	 	  = async (params = {}) => await vm.$u.post('/addons/miniform/index/signin',params);
	
	vm.$api.alilogin 	 	  = async (params = {}) => await vm.$u.post('/addons/miniform/login/aliLogin',params);

}

export default {
	install
}
