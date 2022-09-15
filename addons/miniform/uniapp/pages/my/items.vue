<template>
	<view>
		<!-- 顶部导航 -->
		<fa-navbar title="我的项目" :border-bottom="false"></fa-navbar>

		<view class="list u-p-30">
			<view class="item bg-white" v-for="(item, index) in list" :key="index" @click="goPage">
				<view class="u-flex u-p-30" @click="goPage('/pages/my/detail?id=' + item.id)">
					<image class="thumb" :src="item.project && item.project.image" mode="aspectFill"></image>
					<view class="u-flex-1 u-p-l-30 right-item">
						<view class="title u-line-2 u-font-16" v-text="item.project && item.project.title"></view>
						<view class="" v-if="item.order">
							费用：
							<text class="price u-font-14">￥{{ item.order.amount }}</text>
						</view>
						<view class="free" v-if="!item.order">免费</view>
						<view class="u-p-t-10 u-tips-color u-font-24">
							<u-icon name="clock"></u-icon>
							<text class="u-m-l-10" v-text="item.createtime_text"></text>
						</view>
					</view>
				</view>
				
				<!-- #ifndef MP-WEIXIN -->
				<view class="u-border-top u-p-30  u-flex u-row-right">
				<!-- #endif -->
				
				<!-- #ifdef MP-WEIXIN -->
				<view class="u-border-top u-p-30  u-flex u-row-between">					
					<view class="">						
						<u-button type="primary" v-if="!(item.subscribe && item.subscribe.id) && vuex_config.sendnoticemode != 'disabled'" size="mini" @click="subscribeMessage(item.id,index)">订阅通知</u-button>
					</view>
				<!-- #endif -->
				
					<view class="u-flex">
						<view class="" v-if="item.project.status_ing != '已结束' && !item.is_signin && !item.is_verification && ['refunding','canceled','expired'].indexOf(item.status) == -1">
							<u-button type="default" size="mini" @click="cancelConfirm(item.id,index)">取消</u-button>
						</view>
						<view class="u-m-l-10" v-if="item.project.status_ing == '已结束'">
							<u-button type="default" :disabled="true" size="mini">已结束</u-button>
						</view>
						<view class="u-m-l-10" v-if="item.status == 'refunding'">
							<u-button type="default" :disabled="true" size="mini">退款中</u-button>
						</view>
						<view class="u-m-l-10" v-if="item.status == 'canceled'">
							<u-button type="default" :disabled="true" size="mini">已取消</u-button>
						</view>
						<view class="u-m-l-10" v-if="item.status == 'expired'">
							<u-button type="default" :disabled="true" size="mini">已过期</u-button>
						</view>
						<view class="u-m-l-10">
							<u-button
								type="primary"
								hover-class="none"
								:custom-style="{ backgroundColor: theme.bgColor, color: theme.color }"
								size="mini"
								@click="goPage('/pages/detail/detail?id=' + item.project_id)"
							>
								项目信息
							</u-button>
						</view>
						<view
							class="u-m-l-10"
							v-if="item.project.status_ing != '已结束' && ['refunding', 'canceled', 'nonpayment'].indexOf(item.status) == -1 && item.project.open_signin == 1"
						>
							<u-button type="warning" hover-class="none" size="mini" v-if="!item.is_signin" @click="goSignin(item.id,index)">签到</u-button>
							<u-button type="warning" hover-class="none" :disabled="true" size="mini" v-else>已签到</u-button>
						</view>
						<view class="u-m-l-10" v-if="item.status == 'nonpayment' && item.project.status_ing != '已结束'">
							<u-button type="error" size="mini" @click="goPage('/pages/payment/payment?id=' + item.id)">立即支付</u-button>
						</view>
						<view class="u-m-l-10" v-if="item.order && item.order.status == 'paid'"><u-button type="success" size="mini">已支付</u-button></view>
					</view>
				</view>
			</view>
		</view>
		<!-- 空数据 -->
		<view class="u-u-p-t-80 u-m-t-80 u-p-b-80" v-if="is_empty"><u-empty text="没有更多的数据了..."></u-empty></view>
		<!-- 加载更多 -->
		<view class="u-p-b-30" v-if="list.length"><u-loadmore :status="has_more ? status : 'nomore'" /></view>
		<!-- 回到顶部 -->
		<u-back-top :scroll-top="scrollTop" :icon-style="{ color: theme.bgColor }" :custom-style="{ backgroundColor: theme.lightColor }"></u-back-top>
		<u-modal v-model="cancelConfirmShow" content="确定取消？" :show-cancel-button="true" @confirm="confirm"></u-modal>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
	</view>
</template>

<script>
export default {	
	onLoad() {
		this.myProject();
	},
	data() {
		return {
			list: [],
			cancelConfirmShow: false,
			id: 0,
			index: 0,
			page: 1,
			status: 'loadmore',
			has_more: false,
			scrollTop: 0,
			is_empty:false
		};
	},
	methods: {
		myProject() {
			this.$api
				.myProject({
					page: this.page
				})
				.then(res => {
					if (res.code) {
						this.list = [...this.list, ...res.data.data];
						this.has_more = res.data.current_page < res.data.last_page;
					}
					this.is_empty = !this.list.length;
				});
		},
		cancel(id, index) {
			this.$api
				.cancel({
					id: id
				})
				.then(res => {
					this.$u.toast(res.msg);
					if (res.code) {
						this.$set(this.list[index], 'status', this.list[index].order && this.list[index].order.status == 'paid' ? 'refunding' : 'canceled');
						if (this.list[index].order) {
							this.list[index].order.status = this.list[index].order.status == 'paid' ? 'refunding' : 'canceled';
						}
					}
				});
		},
		cancelConfirm(id, index) {
			this.cancelConfirmShow = true;
			this.id = id;
			this.index = index;
		},
		confirm() {
			this.cancel(this.id, this.index);
		},
		//签到
		goSignin(id,index) {
			let that = this;
			uni.getLocation({
				type: 'wgs84',
				success: function(res) {
					console.log(res)
					that.$api.toSignin({ id: id, longitude: res.longitude, latitude: res.latitude }).then(res => {
						that.$u.toast(res.msg);
						if(res.code==1){
							that.$set(that.list[index],'is_signin',1)
						}
					});
				},
				fail(res) {
					that.$u.toast(JSON.stringify(res))
				}
			});
		},
		// #ifdef MP-WEIXIN
		subscribeMessage(id,index) {
			if(!this.vuex_user.is_third){
				this.$u.route('/pages/login/wxlogin');
				return;
			}
			wx.requestSubscribeMessage({
				tmplIds: this.vuex_config.tpl_ids,
				complete: res => {
					console.log(res);
					if(res.errMsg == 'requestSubscribeMessage:ok'){
						this.$api.subscribe({tpl_ids:res,logs_id:id}).then(res=>{
							this.$u.toast('订阅成功！')
							this.$set(this.list[index],'subscribe',{id:1})							
						})
					}
				}
			});
		},
		// #endif
	},
	onPageScroll(e) {
		this.scrollTop = e.scrollTop;
	},
	onReachBottom() {
		if (this.has_more) {
			this.status = 'loading';
			this.page++;
			this.myProject();
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
.list {
	padding-bottom: 30rpx;

	.item {
		border-bottom: 1px solid #f4f6f8;

		&:not(:last-child) {
			margin-bottom: 30rpx;
		}

		.thumb {
			width: 200rpx;
			height: 150rpx;
			border-radius: 10rpx;
		}

		.right-item {
			height: 150rpx;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 10rpx 0;
		}

		.title {
			// font-weight: bold;
		}

			.price {
				font-size: 28rpx;
			}

			.free {
				color: $u-type-success-dark;
			}
		}
	}
</style>
