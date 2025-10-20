import { createRouter, createWebHashHistory,createWebHistory, type RouteRecordRaw } from 'vue-router'
import ZhaomuCloud from '@/views/admin/ZhaomuCloud.vue'
import Layout from '@/views/admin/Layout.vue'
import Products from '@/views/admin/Products.vue'
import Orders from '@/views/admin/Orders.vue'
import Introduction from '@/views/admin/Introduction.vue'
import UserLayout from '@/views/user/Layout.vue'
import UserProducts from '@/views/user/Products.vue'
import UserOrders from '@/views/user/Orders.vue'
import Panel from '@/views/panel/index.vue'

const isAdmin = window.APP_CONFIG.isAdmin
const isPanel = window.APP_CONFIG.customParam
const routes: RouteRecordRaw[] = isAdmin ? [
  {
    path: '/',
    component: Layout,
    children: [
      {
        path: '',
        redirect: '/buy'
      },

      {
        path: 'buy',
        name: 'AdminBuy',
        component: Products
      },
      {
        path: 'orders',
        name: 'AdminOrders',
        component: Orders
      },
      {
        path: 'introduction',
        name: 'AdminIntroduction',
        component: Introduction
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: ZhaomuCloud
      }
    ]
  }
] : isPanel ? [
  {
    /**path改成路径**/
    path: '/:pathMatch(.*)*',
    component: Panel
  }
] : [
  {
    path: '/',
    component: UserLayout,
    children: [
      {
        path: '',
        redirect: '/buy'
      },
      {
        path: 'buy',
        name: 'UserBuy',
        component: UserProducts
      },
      {
        path: 'orders',
        name: 'UserOrders',
        component: UserOrders
      }

    ]
  }

]

const router = createRouter({
  history: isPanel ? createWebHistory() : createWebHashHistory(),
  routes
})

export default router
