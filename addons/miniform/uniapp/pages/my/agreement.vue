<template>
	<view class="u-bg-white">
		<!-- 顶部导航 -->
		<fa-navbar title="用户协议"></fa-navbar>		
		<view class="u-p-30 u-line-height">
			<u-parse :html="vuex_config.agreement" :tag-style="vuex_parse_style" :domain="vuex_config && vuex_config.upload && vuex_config.upload.cdnurl? vuex_config.upload.cdnurl : ''"></u-parse>
		</view>
		<view class="u-p-b-30 u-text-center u-tips-color">版权所有Copyright © 2017-2020</view>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
	</view>
</template>

<script>
export default {	
	data() {
		return {};
	},
	methods: {}
};
</script>

<style lang="scss">
page {
	background-color: #f4f6f8;
}
</style>
