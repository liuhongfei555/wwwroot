<template>
	<view>
		<!-- 顶部导航 -->
		<fa-navbar title="个人中心"></fa-navbar>
		<view class="">
			<view class="u-flex user-box u-p-30 bg-white" @click="goToLogin">
				<view class="u-m-r-10"><u-avatar :src="vuex_user.avatar" size="140"></u-avatar></view>
				<view class="u-flex-1 u-m-l-20" v-if="vuex_token">
					<view class="u-font-18 u-p-b-20" v-text="vuex_user.nickname"></view>
					<view class="u-font-14 u-tips-color" v-text="vuex_user.bio"></view>
				</view>
				<view class="u-flex-1 u-m-l-20" v-else><view class="u-font-18 u-p-b-20">登录</view></view>
				<view class="u-m-l-10 u-p-10"><!-- <u-icon name="scan" color="#969799" size="28"></u-icon> --></view>
				<view class="u-m-l-10 u-p-10"><!-- <u-icon name="arrow-right" color="#969799" size="28"></u-icon> --></view>
			</view>		
			<view class="u-m-t-20">
				<u-cell-group>
					<u-cell-item icon="grid" title="我的项目" @click="goPage('/pages/my/items', 1)"></u-cell-item>
					<u-cell-item icon="phone" title="联系我们" @click="callPhone"></u-cell-item>
					<u-cell-item icon="heart" title="每日一签" @click="toSignin"></u-cell-item>
					<u-cell-item v-if="vuex_token" icon="setting" title="注销登录" @click="goOut"></u-cell-item>
				</u-cell-group>
			</view>
		</view>
		<u-top-tips ref="uTips" :navbar-height="statusBarHeight + navbarHeight"></u-top-tips>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
	</view>
</template>

<script>
export default {
	data() {
		return {
			// 状态栏高度，H5中，此值为0，因为H5不可操作状态栏
			statusBarHeight: uni.getSystemInfoSync().statusBarHeight,
			// 导航栏内容区域高度，不包括状态栏高度在内
			navbarHeight: 44,
		};
	},
	onLoad() {
		console.log(this.vuex_config);
	},
	onShow() {
		if (this.vuex_token) {
			this.getUserIndex();
		}
	},
	methods: {	
		goToLogin() {
			if (!this.vuex_token) {
				this.$u.route('/pages/login/mobilelogin');
			}
		},
		goOut() {
			this.$u.vuex('vuex_token', '');
			this.$u.vuex('vuex_user', {});
		},
		getUserIndex() {			
			let apptype = '';
			let platform = '';
			
			// #ifdef MP-WEIXIN
			platform = 'wechat';
			apptype = 'miniapp';
			// #endif
			
			this.$api.getUserIndex({apptype,platform}).then(res => {
				uni.stopPullDownRefresh();
				if (res.code) {
					this.$u.vuex('vuex_user', res.data.userInfo);
				}
			});
		},
		toSignin(){
			if (!this.vuex_user.is_install_signin) {
				this.$refs.uTips.show({
					title: '请先安装会员签到插件插件或启用该插件',
					type: 'error',
					duration: '3000'
				});
			
				return;
			}
			this.goPage('/pages/signin/signin',1);
		},
		callPhone() {
			uni.makePhoneCall({
				phoneNumber: this.vuex_config.phone
			});
		}
	},
	//下拉刷新
	onPullDownRefresh() {
		if (this.vuex_token) {
			this.getUserIndex();
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #f4f6f8;
}
</style>
<style lang="scss" scoped>

</style>
