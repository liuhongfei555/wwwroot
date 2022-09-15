<template>
	<view>
		<fa-navbar title="提交成功" :border-bottom="false"></fa-navbar>		
		<view class="success">
			<u-icon name="checkmark-circle-fill" color="#999" size="150"></u-icon>
			<view class="u-m-t-30 u-font-40">
				提交成功！
			</view>
			<view class="u-m-t-30" style="width: 60vw;">
				提示内容提示内容提示内容提示内容提示内容提示
			</view>
		</view>
		<view class="foot">
			<view class="">
				<u-button type="primary" hover-class="none" :custom-style="{ backgroundColor: theme.bgColor, color: theme.color }" shape="circle">查看详情</u-button>
			</view>
			<view class="u-m-t-20 u-m-b-20">
				<u-button type="primary" hover-class="none" :custom-style="{ backgroundColor: theme.bgColor, color: theme.color }" shape="circle">分享好友</u-button>
			</view>
			<view class="">
				<u-button type="primary" hover-class="none" :custom-style="{ backgroundColor: theme.bgColor, color: theme.color }" shape="circle" @click="goPage('/pages/index/index')">返回首页</u-button>
			</view>
		</view>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
	</view>
</template>

<script>
export default {
	onLoad() {
		
	},
	data() {
		return {
			
		};
	},
	methods: {
		
	}
};
</script>

<style lang="scss">
page {
	background-color: #FFFFFF;
}
</style>
<style lang="scss" scoped>
	.success{
		display: flex;
		width: 100vw;
		flex-direction: column;
		height: 50vh;
		align-items: center;
		justify-content: center;
	}
	.foot{
		padding: 30rpx 60rpx;
	}
</style>