<template>
	<view>
		<view class="u-p-30 u-border-bottom u-flex u-col-center">
			<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
			<view class="u-item-title" v-text="item.title"></view>
		</view>
		<view class="content u-p-30" v-if="item.type == 'images'"><u-swiper :list="item.value" height="300" @click="swiper"></u-swiper></view>
		<view class="content u-p-30" v-else><image :src="item.value" mode="aspectFill" @click="look" style="width: 100%;height: 300rpx;"></image></view>
	</view>
</template>

<script>
export default {
	props: {
		item: {
			type: Object,
			default: {}
		}
	},
	data() {
		return {};
	},
	methods: {
		swiper(e) {
			this.lookImage(e, this.item.value);
		},
		look() {
			this.lookImage(0, [this.item.value]);
		},
		//预览图片
		lookImage(index, images) {
			uni.previewImage({
				current: index,
				urls: images,
				longPressActions: {
					itemList: ['发送给朋友', '保存图片', '收藏'],
					success: function(data) {
						console.log(data);
					},
					fail: function(err) {
						console.log(err.errMsg);
					}
				}
			});
		}
	}
};
</script>

<style lang="scss"></style>
