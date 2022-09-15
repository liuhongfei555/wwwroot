<template>
	<view class="u-skeleton">
		<!-- 顶部导航 -->
		<fa-navbar title="订单支付" :border-bottom="false"></fa-navbar>
		<view class="u-p-30 u-flex bg-white">
			<image class="thumb u-skeleton-rect" :src="order.project && order.project.image"></image>
			<view class="u-m-l-20 order">
				<view class="u-skeleton-rect" v-text="order.project && order.project.title"></view>
				<view class="price u-skeleton-rect">￥{{ order.amount }}</view>
			</view>
		</view>
		<view class="bg-white u-m-t-30">
			<u-cell-group title="支付方式">
				<u-radio-group v-model="paytype" @change="radioGroupChange">
					<view class="paytype">
						<!-- #ifndef MP-ALIPAY -->
						<u-cell-item
							:icon-style="{ color: '#20D029' }"
							@click="paytype = 'wechat'"
							:arrow="false"
							icon="weixin-circle-fill"
							title="微信支付"
							hover-class="cell-hover-class"
						>
							<u-radio slot="right-icon" name="wechat" :active-color="theme.bgColor"></u-radio>
						</u-cell-item>
						<!-- #endif -->

						<!-- #ifndef MP-WEIXIN -->
						<u-cell-item
							:icon-style="{ color: '#00A1E9' }"
							@click="paytype = 'alipay'"
							:arrow="false"
							icon="zhifubao-circle-fill"
							title="支付宝支付"
							hover-class="cell-hover-class"
						>
							<u-radio slot="right-icon" name="alipay" :active-color="theme.bgColor"></u-radio>
						</u-cell-item>
						<!-- #endif -->
					</view>
				</u-radio-group>
			</u-cell-group>
		</view>
		<view class="payment bg-white">
			<u-button type="primary" hover-class="none" :custom-style="{ backgroundColor: theme.bgColor, color: theme.color }" shape="circle" @click="submit">
				立即支付
			</u-button>
		</view>
		<u-skeleton :loading="loading" :animation="true" bgColor="#FFF"></u-skeleton>
	</view>
</template>

<script>
import {loginfunc} from '@/common/fa.mixin.js'
export default {
	mixins:[loginfunc],
		onLoad(e) {
			this.id = e.id || '';
			this.orderid = e.orderid || '';
			this.getOrder();
		},
		mounted() {
			// hash模式 支付#号前加?号，让微信处理为参数
			// #ifdef H5
			let url = window.location.href;
			   　if(this.$util.isWeiXinBrowser() && url.indexOf('#') != -1 && !url.match(/\?#/)) {
			      　location.replace(window.location.href.split('#')[0] + '?' + window.location.hash);
			     }
			// #endif
		},
		data() {
			return {
				// #ifndef MP-ALIPAY
				paytype: 'wechat',
				// #endif
				// #ifdef MP-ALIPAY
				paytype: 'alipay',
				// #endif
				id: '',
				orderid: '',
				order: {},
				loading: true, // 是否显示骨架屏组件
			};
		},
		methods: {
			radioGroupChange(e) {
				this.paytype = e;
			},
			getOrder() {
				this.$api.order({
					id: this.id,
					orderid: this.orderid
				}).then(res => {
					if (res.code) {
						this.loading = false;
						this.order = res.data;
					} else {
						this.$u.toast(res.msg)
						setTimeout(() => {
							if (res.data.project_id) {
								this.$u.route('/pages/my/items');
							} else {
								uni.navigateBack({
									delta: 1
								})
							}
						}, 1000)
					}
				})
			},
			// #ifdef MP-WEIXIN
			submit: async function() {
				let res = await this.$api.payment({
					id: this.id,
					orderid: this.orderid,
					paytype: this.paytype,
					method: 'miniapp'
				});
				if (res.data == 'bind') {
					this.$u.route('/pages/login/wxlogin');
					return;
				}
				if (!res.code) {
					this.$u.toast(res.msg);
					return;
				}
				uni.requestPayment({
					provider: 'wxpay',
					timeStamp: res.data.timeStamp,
					nonceStr: res.data.nonceStr,
					package: res.data.package,
					signType: res.data.signType,
					paySign: res.data.paySign,
					success: rest => {
						this.$u.toast('支付成功！');
						wx.requestSubscribeMessage({
						  tmplIds: this.vuex_config.tpl_ids,
						  complete:(res)=>{
							console.log(res)
							if(res.errMsg == 'requestSubscribeMessage:ok'){
								this.$api.subscribe({tpl_ids:res,logs_id:this.id}).then(res=>{
									console.log(res)
								})
							}
							this.$u.route('/pages/my/items')
						  }
						})
					},
					fail: err => {
						this.$u.toast('fail:' + JSON.stringify(err));
					}
				});
			},
			// #endif

			// #ifdef MP-ALIPAY
			submit: async function() {
				this.$u.toast('支付宝小程序未支持支付');
				//需要支付需先自行对接
				return;
				let res = await this.$api.payment({
					id: this.id,
					orderid: this.orderid,
					paytype: 'alipay',
					method: 'mini'
				});
				if (res.data == 'bind') {
					this.$u.route('/pages/login/wxlogin');
					return;
				}
				if (!res.code) {
					this.$u.toast(res.msg);
					return;
				}
				uni.requestPayment({
					provider: 'alipay',
					orderInfo:res.data.trade_no,
					success: rest => {
						this.$u.toast('支付成功！');
						setTimeout(()=>{
							this.$u.route('/pages/my/items')
						},1500)
					},
					fail: err => {
						this.$u.toast('fail:' + JSON.stringify(err));
					}
				});
			},
			// #endif

			// #ifdef H5
			submit: async function() {

				let data = {
					id: this.id,
					orderid: this.orderid,
					paytype: this.paytype,
					method: 'wap'
				};
				//在微信环境，且为微信支付
				if (this.$util.isWeiXinBrowser() && this.paytype == 'wechat') {
					data.method = 'mp';
					let res = await this.$api.payment(data);
					if (res.data == 'bind') {
						this.goAuth();
						return;
					};
					if (!res.code) {
						this.$u.toast(res.msg);
						return;
					}
					window.WeixinJSBridge.invoke(
						'getBrandWCPayRequest', {
							appId: res.data.appId, // 公众号名称，由商户传入
							timeStamp: res.data.timeStamp, // 时间戳，自1970年以来的秒数
							nonceStr: res.data.nonceStr, // 随机串
							package: res.data.package,
							signType: res.data.signType, // 微信签名方式：
							paySign: res.data.paySign // 微信签名
						},
						rest => {
							if (rest.err_msg === 'get_brand_wcpay_request:ok') {
								this.$u.toast('支付成功！');
								this.$u.route('/pages/my/items')
							} else if (rest.err_msg === 'get_brand_wcpay_request:cancel') {
								this.$u.toast('取消支付');
							} else {
								this.$u.toast('支付失败');
							}
						}
					);
				} else {

					data.returnurl = window.location.href;
					let res = await this.$api.payment(data);
					if (!res.code) {
						this.$u.toast(res.msg);
						return;
					}
					//URL地址
					if (res.data.toString().match(/^((?:[a-z]+:)?\/\/)(.*)/i)) {
						location.href = res.data;
						return;
					}

					//Form表单
					document.getElementsByTagName("body")[0].innerHTML = res.data;
					let form = document.querySelector("form");
					if (form && form.length > 0) {
						form.submit();
						return;
					}

					//Meta跳转
					let meta = document.querySelector('meta[http-equiv="refresh"]');
					if (meta && meta.length > 0) {
						setTimeout(function() {
							location.href = meta.content.split(/;/)[1];
						}, 300);
						return;
					}
				}
			}

			// #endif

			// #ifdef APP-PLUS
			submit: async function() {

				let appid = plus.runtime.appid;

				let res = await this.$api.payment({
					id: this.id,
					orderid: this.orderid,
					paytype: this.paytype,
					method: 'app',
					appid: appid
				});
				if (!res.code) {
					this.$u.toast(res.msg);
					return;
				}
				uni.requestPayment({
					provider: this.paytype == 'alipay' ? 'alipay' : 'wxpay',
					orderInfo: res.data, //微信、支付宝订单数据
					success: function(rest) {
						this.$u.toast('支付成功！');
						this.$u.route('/pages/my/items')
					},
					fail: function(err) {
						console.log('fail:' + JSON.stringify(err));
					}
				});
			}
			// #endif

		}
	};
</script>

<style lang="scss">
page {
	background-color: #f4f6f8;
}
</style>
<style lang="scss" scoped>
.thumb {
	width: 150rpx;
	height: 150rpx;
	border-radius: 10rpx;
}

.order {
	height: 150rpx;
	padding: 10rpx 0;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.paytype {
	width: 100vw;
}

.payment {
	position: fixed;
	bottom: 0;
	left: 0;
	width: 100%;
	padding: 30rpx 80rpx;
}
</style>
