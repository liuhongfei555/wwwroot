<template>
	<view class="">
		<!-- 顶部导航 -->
		<fa-navbar title="授权登录"></fa-navbar>
		<view class="content">
			<view>
				<view class="login-item">
					<view class="logo">
						<!-- #ifdef MP-WEIXIN -->
						<open-data type="userAvatarUrl"></open-data>
						<!-- #endif -->
						<!-- #ifdef MP-ALIPAY -->
						<image src="../../static/tabbar/my.png"></image>
						<!-- #endif -->
					</view>
				</view>

				<view class="login-tip">
					<view>申请获取以下权限</view>
					<view>获得你的公开信息 （昵称、头像等）</view>
				</view>
			</view>
			<view class="u-flex u-row-between">
				<!-- #ifdef MP-WEIXIN -->
				<u-button hover-class="none" shape="circle" @click="handleRefuse" class="u-flex-6" :custom-style="{ width: '95%' }">拒绝</u-button>
				<u-button
					hover-class="none"
					type="primary"
					shape="circle"
					class="u-flex-6"
					:custom-style="{ width: '95%', backgroundColor: theme.bgColor, color: theme.color }"
					@click="getUserInfo"
				>
					允许
				</u-button>
				<!-- #endif -->

				<!-- #ifdef MP-ALIPAY -->
				<u-button hover-class="none" shape="circle" @click="handleRefuse" class="u-flex-6" :custom-style="{ width: '46%' }">拒绝</u-button>
				<button
					class="u-flex-6 u-m-l-15"
					open-type="getAuthorize"
					:style="{ width: '46%', backgroundColor: theme.bgColor, color: theme.color, borderRadius: '100rpx', height: '80rpx', lineHeight: '80rpx' }"
					@getAuthorize="getALICode"
					scope="userInfo"
				>
					允许
				</button>
				<!-- #endif -->
			</view>
			<u-top-tips ref="uTips"></u-top-tips>
		</view>
	</view>
</template>

<script>
import { loginfunc } from '@/common/fa.mixin.js';
export default {
	mixins: [loginfunc],
	onLoad(e) {
		this.index = parseInt(e.index) || 1;
	},
	data() {
		return {
			index: 1
		};
	},
	methods: {
		handleRefuse() {
			this.$u.toast('未授权');
			setTimeout(() => {
				uni.navigateBack({
					delta: 1
				});
			}, 1000);
		},
		// #ifdef MP-WEIXIN
		getCode: async function() {
			return new Promise((resolve, reject) => {
				uni.login({
					success: function(res) {
						if (res.code) {
							resolve(res.code);
						} else {
							//login成功，但是没有取到code
							reject('未取得code');
						}
					},
					fail: function(res) {
						reject('用户授权失败wx.login');
					}
				});
			});
		},
		//用户授权得到用户的信息
		getUserInfo: function() {
			let that = this;
			wx.getUserProfile({
				lang: 'zh',
				desc: '用户信息绑定',
				success: async function(e) {
					console.log(e);
					try {
						let code = await that.getCode();
						let data = {
							code: code,
							rawData: e.userInfo,
							__token__: that.vuex__token__
						};
						//有推荐码的话，带上
						if (that.vuex_invitecode) {
							data.invitecode = that.vuex_invitecode;
						}
						that.toLogin(data);
					} catch (e) {
						that.$u.toast(e);
					}
				},
				fail: function(e) {
					console.log(e);
					that.$u.toast(JSON.stringify(e));
				}
			});
		},
		//实际的去登陆
		toLogin: async function(data) {
			let res = await this.$api.gowxLogin(data);
			if (!res.code) {
				this.$u.toast(res.msg);
				return;
			}
			if (res.data.user) {
				this.$u.vuex('vuex_token', res.data.user.token);
				this.success(this.index);
				return;
			}
			this.$u.vuex('vuex_third', res.data.third);
			//授权成功到登录或绑定页面
			this.$u.route('/pages/login/register?bind=bind');
		},
		// #endif
		// #ifdef MP-ALIPAY
		getALICode(e) {
			let that = this;
			uni.login({
				scopes: 'auth_base',
				success: res => {
					if (res.authCode) {
						uni.getUserInfo({
							provider: 'alipay',
							success: function(infoRes) {								
								let code = res.authCode;
								if(code.includes('&')){
									code = code.split('&').shift()
								}
								that.aLiLoginStep(code, infoRes.userInfo);
							},
							fail: function(errorRes) {
								that.$u.toast('未取得用户昵称头像信息');
							}
						});
					} else {
						that.$u.toast('未取得code');
					}
				},
				fail: function(res) {
					console.log(res)
					that.$u.toast('用户授权失败my.login');
				}
			});
		},
		aLiLoginStep: function(code, userinfo) {
			this.$api
				.alilogin({
					code: code,
					userinfo: userinfo
				})
				.then(res => {
					console.log(res);
					if (!res.code) {
						this.$u.toast(res.msg);
						return;
					}
					if (res.data.user) {
						this.$u.vuex('vuex_token', res.data.user.token);
						this.success(this.index);
						return;
					}
					this.$u.vuex('vuex_third', res.data.third);
					//授权成功到登录或绑定页面
					this.$u.route('/pages/login/register?bind=bind&index='+this.index);
				});
		}
		// #endif
	}
};
</script>

<style lang="scss">
.content {
	background-color: #fff;
	height: 100vh;
	padding: 100rpx 60rpx 0;
}

.login-item {
	display: flex;
	justify-content: center;
	padding-bottom: 40rpx;
	border-bottom: 1rpx solid #dddddd;
}

.logo {
	display: block;
	width: 180rpx;
	height: 180rpx;
	border-radius: 50%;
	overflow: hidden;
	border: 2px solid #fff;
	box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
}

.login-tip {
	padding: 60rpx 0;
	&-big {
		font-size: 28rpx;
		line-height: 80rpx;
	}
	&-small {
		font-size: 12px;
		color: #9e9e9e;
	}
}
</style>
