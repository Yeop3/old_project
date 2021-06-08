import store from '@state/store';

export default [
    {
        path: '/',
        name: 'home',
        redirect: {name: 'index-orders', query: {'order': 'all'}},
        component: () => lazyLoadView(import('@views/home')),
        meta: {
            authRequired: true,
        },
        props: (route) => ({user: store.state.auth.currentUser || {}}),
    },
    {
        path: '/login',
        name: 'login',
        component: () => lazyLoadView(import('@views/login')),
        meta: {
            // beforeResolve(routeTo, routeFrom, next) {
            //     // If the user is already logged in
            //     if (store.getters['auth/loggedIn']) {
            //         // Redirect to the home page instead
            //         next({name: 'index-orders'});
            //     } else {
            //         // Continue to the login page
            //         next();
            //     }
            // },
        },
    },

    {
        path: '/confirm-account',
        name: 'confirm-account',
        component: () => lazyLoadView(import('@views/confirm')),
        meta: {
            beforeResolve(routeTo, routeFrom, next) {
                // If the user is already logged in
                if (store.getters['auth/loggedIn']) {
                    // Redirect to the home page instead
                    next({name: 'home'});
                } else {
                    // Continue to the login page
                    next();
                }
            },
        },
    },
    {
        path: '/forget-password',
        name: 'forget-password',
        component: () => lazyLoadView(import('@views/forgetPassword')),
        meta: {
            beforeResolve(routeTo, routeFrom, next) {
                // If the user is already logged in
                if (store.getters['auth/loggedIn']) {
                    // Redirect to the home page instead
                    next({name: 'home'});
                } else {
                    // Continue to the login page
                    next();
                }
            },
        },
    },
    {
        path: '/logout',
        name: 'logout',
        meta: {
            authRequired: true,
            beforeResolve(routeTo, routeFrom, next) {
                store.dispatch('auth/logOut');
                const authRequiredOnPreviousRoute = routeFrom.matched.some(
                    (route) => route.meta.authRequired
                );
                // Navigate back to previous page, or home as a fallback
                next(authRequiredOnPreviousRoute ? {name: 'home'} : {...routeFrom});
            },
        },
    },
    {
        path: '/404',
        name: '404',
        component: require('@views/_404').default,
        // Allows props to be passed to the 404 page through route
        // params, such as `resource` to define what wasn't found.
        props: true,
    },
    {
        path: '/403',
        name: '403',
        component: require('@views/_403').default,
        // Allows props to be passed to the 404 page through route
        // params, such as `resource` to define what wasn't found.
        props: true,
    },

    {
        path: '/drivers/',
        name: 'index-drivers',
        component: () => lazyLoadView(import('@views/driver/index')),
        meta: {
            authRequired: true,
            /*beforeResolve(routeTo, routeFrom, next) {
                store.dispatch('drivers/getIndex', {page: routeTo.query.page}).then((res) => {
                    next();
                });
            },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/drivers/create',
        name: 'create-driver',
        component: () => lazyLoadView(import('@views/driver/create')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/drivers/edit/:id',
        name: 'edit-driver',

        component: () => lazyLoadView(import('@views/driver/edit')),
        meta: {
            authRequired: true,
            /*beforeResolve(routeTo, routeFrom, next) {
                store.dispatch('drivers/getById', routeTo.params.id).then((res) => {
                    next();
                }).catch(() => next({name: 'index-drivers'}));
            },*/
        }
    },

    {
        path: '/product-types/',
        name: 'index-product-types',
        component: () => lazyLoadView(import('@views/product-types/index')),
        meta: {
            authRequired: true,
            /* beforeResolve(routeTo, routeFrom, next) {
                 store.dispatch('productTypes/getIndex', {page: routeTo.query.page}).then((res) => {
                     next();
                 }).catch((error) => {
                     next();
                 });
             },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/product-types/create',
        name: 'create-product-types',
        component: () => lazyLoadView(import('@views/product-types/create')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/product-types/edit/:id',
        name: 'edit-product-types',

        component: () => lazyLoadView(import('@views/product-types/edit')),
        meta: {
            authRequired: true,
            /*beforeResolve(routeTo, routeFrom, next) {
                store.dispatch('productTypes/getById', routeTo.params.id).then((res) => {
                    next();
                }).catch(() => {
                    next({name: 'index-product-types'});
                });
            },*/
        }
    },
    {
        path: '/product-types/show/:id',
        name: 'show-product-types',

        component: () => lazyLoadView(import('@views/product-types/show')),
        meta: {
            authRequired: true,
            /* beforeResolve(routeTo, routeFrom, next) {
                 store.dispatch('productTypes/getById', routeTo.params.id).then((res) => {
                     next();
                 }).catch(() => {
                     next({name: 'index-product-types'});
                 });
             },*/
        }
    },


    {
        path: '/locations/',
        name: 'index-location',
        component: () => lazyLoadView(import('@views/locations/index')),
        meta: {
            authRequired: true,
            /* beforeResolve(routeTo, routeFrom, next) {
                 store.dispatch('location/getIndex', routeTo.query.page).then((res) => {
                     next();
                 }).catch((error) => {
                     next();
                 });
             },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/locations/create',
        name: 'create-location',
        component: () => lazyLoadView(import('@views/locations/create')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {

                await store.dispatch('location/getSelect');

                next();
            },
        },
    },
    {
        path: '/locations/edit/:id',
        name: 'edit-location',
        component: () => lazyLoadView(import('@views/locations/edit')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/locations/show/:id',
        name: 'show-location',
        component: () => lazyLoadView(import('@views/locations/show')),
        meta: {
            authRequired: true,
            /* beforeResolve(routeTo, routeFrom, next) {
                 store.dispatch('location/getById', routeTo.params.id).then((res) => {
                     next();
                 }).catch((error) => {
                     next({name: 'index-location'});
                 });
             },*/
        },
    },

    {
        path: '/products/',
        name: 'index-products',
        component: () => lazyLoadView(import('@views/products/index')),
        meta: {
            authRequired: true,
            /*  async beforeResolve(routeTo, routeFrom, next) {
                  await store.dispatch('product/getIndex', {page: routeTo.query.page});
                  await store.dispatch('location/getSelect', 1);
                  await store.dispatch('drivers/getSelectDriver');
                  await store.dispatch('productTypes/getSelectProductType');
                  await store.dispatch('product/getStatusList');
                  next();
              },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/products/create',
        name: 'create-product',
        component: () => lazyLoadView(import('@views/products/create')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                await store.dispatch('location/getSelect');
                await store.dispatch('drivers/getSelectDriver');
                await store.dispatch('productTypes/getSelectProductType');
                await store.dispatch('product/getStatusList');
                await store.dispatch('bots/getSelect');
                next();
            },
        },
    },
    {
        path: '/products/import',
        name: 'create-many-product',
        component: () => lazyLoadView(import('@views/products/create-many')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                await store.dispatch('location/getSelect');
                await store.dispatch('drivers/getSelectDriver');
                await store.dispatch('productTypes/getSelectProductType');
                await store.dispatch('product/getStatusList');
                await store.dispatch('bots/getSelect');
                next();
            },
        },
    },
    {
         path: '/products/edit/:id',
         name: 'edit-products',
         component: () => lazyLoadView(import('@views/products/edit')),
         meta: {
             authRequired: true,
             async beforeResolve(routeTo, routeFrom, next) {
                 try {
                     await store.dispatch('product/getById', routeTo.params.id);
                     await store.dispatch('location/getSelect');
                     await store.dispatch('drivers/getSelectDriver');
                     await store.dispatch('productTypes/getSelectProductType');
                     await store.dispatch('product/getStatusList');
                     next();
                 } catch (e) {
                     next({name: 'index-products'});
                 }
             },
         },
    },
    {
        path: '/products/show/:id',
        name: 'show-products',
        component: () => lazyLoadView(import('@views/products/show')),
        meta: {
            authRequired: true,
            /*beforeResolve(routeTo, routeFrom, next) {
                store.dispatch('product/getById', routeTo.params.id).then((res) => {
                    next();
                }).catch((error) => {
                    next({name: 'index-products'});
                });
            },*/
        },
    },

    {
        path: '/stokers/',
        name: 'index-stokers',
        component: () => lazyLoadView(import('@views/stokers/index')),
        meta: {
            authRequired: true,
        },
        // props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/stokers/create',
        name: 'create-stokers',
        component: () => lazyLoadView(import('@views/stokers/create')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                await store.dispatch('location/getSelect');
                await store.dispatch('productTypes/getSelectProductType');
                await store.dispatch('clients/loadClientsForSelect');
                await store.dispatch('bots/getSelect');

                next();
            },
        },
        // props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/stokers/show/:number',
        name: 'show-stokers',
        component: () => lazyLoadView(import('@views/stokers/show')),
        meta: {
            authRequired: true,

        },
        // props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/stokers/edit/:number',
        name: 'edit-stokers',
        component: () => lazyLoadView(import('@views/stokers/edit')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                await store.dispatch('location/getSelect');
                await store.dispatch('productTypes/getSelectProductType');
                await store.dispatch('clients/loadClientsForSelect');
                await store.dispatch('bots/getSelect');

                next();
            },
        },
        // props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/discounts/',
        name: 'index-discounts',
        component: () => lazyLoadView(import('@views/discounts/index')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {
                     await store.dispatch('discounts/getIndex', {page: routeTo.query.page});
                 } catch (e) {
                 }
                 next();
             },*/
        },
        // props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/discounts/create',
        name: 'create-discounts',
        component: () => lazyLoadView(import('@views/discounts/create')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {

                await store.commit('discounts/CLEAR_DISCOUNT');
                await store.dispatch('location/getSelect');
                store.commit('location/LOCATIONS_SELECT_PRODUCT_SHIFT');
                await store.dispatch('productTypes/getSelectProductType');
                store.commit('productTypes/PRODUCT_TYPES_SELECT_SHIFT');

                next();
            },
        },
    },
    {
        path: '/discounts/edit/:id',
        name: 'edit-discounts',
        component: () => lazyLoadView(import('@views/discounts/edit')),
        meta: {
            authRequired: true,
            /*async beforeResolve(routeTo, routeFrom, next) {
                try {
                    await store.dispatch('discounts/getById', routeTo.params.id);
                    await store.dispatch('location/getSelect');
                    store.commit('location/LOCATIONS_SELECT_PRODUCT_SHIFT');
                    await store.dispatch('productTypes/getSelectProductType');
                    store.commit('productTypes/PRODUCT_TYPES_SELECT_SHIFT');
                    next();
                } catch (e) {
                    next({name: 'index-discounts'});
                }
            },*/
        },
    },
    {
        path: '/discounts/show/:id',
        name: 'show-discounts',
        component: () => lazyLoadView(import('@views/discounts/show')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {
                     await store.dispatch('discounts/getById', routeTo.params.id);
                     next();
                 } catch (e) {
                     next({name: 'index-discounts'});
                 }
             },*/
        },
    },

    {
        path: '/operators/',
        name: 'index-operators',
        component: () => lazyLoadView(import('@views/operators/index')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {
                     await store.dispatch('operator/getIndex', routeTo.query.page);
                 } catch (e) {
                 }
                 next();
             },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/operators/create',
        name: 'create-operators',
        component: () => lazyLoadView(import('@views/operators/create')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/operators/edit/:id',
        name: 'edit-operators',
        component: () => lazyLoadView(import('@views/operators/edit')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                try {
                    await store.dispatch('operator/getById', routeTo.params.id);
                    next();
                } catch (e) {
                    next({name: 'index-products'});
                }
            },
        },
    },

    {
        path: '/shifts/',
        name: 'index-shifts',
        component: () => lazyLoadView(import('@views/shifts/index')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {
                     await store.dispatch('shifts/getIndex', {page:routeTo.query.page});
                     await store.dispatch('operator/getSelect');
                 } catch (e) {
                 }
                 next();
             },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/shifts/show/:number',
        name: 'show-shifts',
        component: () => lazyLoadView(import('@views/shifts/show')),
        meta: {
            authRequired: true,
        },
        props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/wallets/qiwi_manual/',
        name: 'index-wallets-qiwi-manual',
        component: () => lazyLoadView(import('@views/wallets/qiwi-manual/index')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {
                     await store.dispatch('qiwiManual/getIndex', routeTo.query.page);
                 } catch (e) {
                 }
                 next();
             },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/wallets/qiwi_manual/create',
        name: 'create-wallets-qiwi-manual',
        component: () => lazyLoadView(import('@views/wallets/qiwi-manual/create')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                next();
            },
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/wallets/qiwi_manual/deleted/',
        name: 'index-wallets-qiwi-manual-deleted',
        component: () => lazyLoadView(import('@views/wallets/qiwi-manual-deleted/index')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {
                     await store.dispatch('qiwiManual/getIndexDeleted', routeTo.query.page);
                 } catch (e) {
                 }
                 next();
             },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/payments/qiwi_manual/',
        name: 'index-payments-wallets-qiwi-manual',
        component: () => lazyLoadView(import('@views/payments/qiwi-manual/index')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {

                 } catch (e) {
                 }
                 next();
             },*/
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/payments/qiwi_manual/create',
        name: 'create-payments-wallets-qiwi-manual',
        component: () => lazyLoadView(import('@views/payments/qiwi-manual/create')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                try {
                    await store.dispatch('order/getSelect');
                } catch (e) {
                }
                next();
            },
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/payments/qiwi_manual/edit/:id',
        name: 'edit-payments-wallets-qiwi-manual',
        component: () => lazyLoadView(import('@views/payments/qiwi-manual/edit')),
        meta: {
            authRequired: true,
            /* async beforeResolve(routeTo, routeFrom, next) {
                 try {

                     next();
                 } catch (e) {
                     console.log(e);
                     next({name: 'index-payments-wallets-qiwi-manual'});
                 }
             },*/
        },
    },

    {
        path: '/orders/',
        name: 'index-orders',
        component: () => lazyLoadView(import('@views/orders/index')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                try {
                    await store.dispatch('order/getStatus');
                } catch (e) {

                }
                next();
            },
        },
        props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/orders/show/:id',
        name: 'show-orders',
        component: () => lazyLoadView(import('@views/orders/show')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                try {
                    await store.dispatch('order/getStatus');
                } catch (e) {

                }
                next();
            },
        },
        props: (route) => ({page: route.query.page || 1}),
    },

    {
        path: '/bots',
        name: 'bots.index',
        component: () => lazyLoadView(import('@views/bot/index')),
        meta: {
            authRequired: true,
        },
        props: (route) => ({page: route.query.page || 1}),
    },
    {
        path: '/bots/create',
        name: 'bots.create',
        component: () => lazyLoadView(import('@views/bot/create')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/bots/card/:number',
        name: 'bots.show',
        component: () => lazyLoadView(import('@views/bot/show')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/bots/update/:number',
        name: 'bots.edit',
        component: () => lazyLoadView(import('@views/bot/edit')),
        meta: {
            authRequired: true,
        }
    },

    {
        path: '/bot-logics',
        name: 'bot-logics.index',
        component: () => lazyLoadView(import('@views/bot_logics/index')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/bot-logics/card/:type/:number',
        name: 'bot-logics.show',
        component: () => lazyLoadView(import('@views/bot_logics/show')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/bot-logics/update/:type/:number',
        name: 'bot-logics.edit',
        component: () => lazyLoadView(import('@views/bot_logics/edit')),
        meta: {
            authRequired: true,
        }
    },

    {
        path: '/clients',
        name: 'clients.index',
        component: () => lazyLoadView(import('@views/clients/index')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/clients/spam-reserve',
        name: 'clients.index.spam.reserve',
        component: () => lazyLoadView(import('@views/clients/indexSpamReserve')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/clients/black-list',
        name: 'clients.index.black-list',
        component: () => lazyLoadView(import('@views/clients/index-black-list')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/clients/hand-dispatch-actual-telegram',
        name: 'index-hand-dispatch-actual-telegram',
        component: () => lazyLoadView(import('@views/clients/index-hand-dispatch-actual-telegram')),
        meta: {
            authRequired: true,
        }
    },
    {
        path: '/clients/:number',
        name: 'clients.show',
        component: () => lazyLoadView(import('@views/clients/show')),
        mete: {
            authRequired: true,
        }
    },
    {
        path: '/clients/update/:number',
        name: 'clients.edit',
        component: () => lazyLoadView(import('@views/clients/edit')),
        meta: {
            authRequired: true,
        }
    },



    {
        path: '/proxies',
        name: 'proxies.index',
        component: () => lazyLoadView(import('@views/proxies/index')),
        mete: {
            authRequired: true,
        }
    },
    {
        path: '/proxies/create',
        name: 'proxies.create',
        component: () => lazyLoadView(import('@views/proxies/create')),
        mete: {
            authRequired: true,
        }
    },
    {
        path: '/proxies/card/:number',
        name: 'proxies.show',
        component: () => lazyLoadView(import('@views/proxies/show')),
        mete: {
            authRequired: true,
        }
    },
    {
        path: '/proxies/update/:number',
        name: 'proxies.edit',
        component: () => lazyLoadView(import('@views/proxies/edit')),
        mete: {
            authRequired: true,
        }
    },
    {
        path: '/settings/common',
        name: 'settings.common',
        component: () => lazyLoadView(import('@views/settings/common')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/settings/reservation',
        name: 'settings.reservation',
        component: () => lazyLoadView(import('@views/settings/reservation')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/settings/crypto-settings',
        name: 'settings.crypto',
        component: () => lazyLoadView(import('@views/settings/crypto')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/settings/payment-methods',
        name: 'settings.payment-methods',
        component: () => lazyLoadView(import('@views/settings/payment-methods')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/statistic',
        name: 'statistic.index',
        component: () => lazyLoadView(import('@views/statistic/index')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                await store.dispatch('location/getSelect');
                await store.dispatch('drivers/getSelectDriver');
                await store.dispatch('productTypes/getSelectProductType');
                await store.dispatch('bots/getSelect');
                next();
            },
        },
    },
    {
        path: '/dispatchers',
        name: 'dispatch.index',
        component: () => lazyLoadView(import('@views/dispatch/index')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {

                next();
            },
        }
    },
    {
        path: '/dispatchers/create',
        name: 'dispatch.create',
        component: () => lazyLoadView(import('@views/dispatch/create')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                await store.dispatch('bots/getSelect');
                next();
            },
        }
    },
    {
        path: '/dispatchers/create-by-exist/:id',
        name: 'dispatch.create-by-exist',
        component: () => lazyLoadView(import('@views/dispatch/create-by-exist')),
        meta: {
            authRequired: true,
            async beforeResolve(routeTo, routeFrom, next) {
                await store.dispatch('bots/load', routeTo.params.id);
                await store.dispatch('dispatcher/getTextProductExist', routeTo.params.id);
                next();
            },
        }
    },
    {
        path: '/crypto-wallet/',
        name: 'crypto-wallet.index',
        component: () => lazyLoadView(import('@views/wallets/crypto/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/crypto-wallet/create',
        name: 'crypto-wallet.create',
        component: () => lazyLoadView(import('@views/wallets/crypto/create')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/crypto-wallet/edit/:id',
        name: 'crypto-wallet.edit',
        component: () => lazyLoadView(import('@views/wallets/crypto/edit')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/crypto-wallet/show/:id',
        name: 'crypto-wallet.show',
        component: () => lazyLoadView(import('@views/wallets/crypto/show')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/crypto-transaction/',
        name: 'crypto-transaction.index',
        component: () => lazyLoadView(import('@views/transaction/crypto/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/kuna-accounts/',
        name: 'kuna-accounts.index',
        component: () => lazyLoadView(import('@views/wallets/kuna/accounts/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/kuna-accounts/create',
        name: 'kuna-accounts.create',
        component: () => lazyLoadView(import('@views/wallets/kuna/accounts/create')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/kuna-accounts/edit/:id',
        name: 'kuna-accounts.edit',
        component: () => lazyLoadView(import('@views/wallets/kuna/accounts/edit')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/kuna-accounts/show/:id',
        name: 'kuna-accounts.show',
        component: () => lazyLoadView(import('@views/wallets/kuna/accounts/show')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/kuna-codes',
        name: 'kuna-codes.index',
        component: () => lazyLoadView(import('@views/wallets/kuna/codes/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/kuna-codes/show/:id',
        name: 'kuna-codes.show',
        component: () => lazyLoadView(import('@views/wallets/kuna/codes/show')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/global-money/wallet',
        name: 'global-money.wallet.index',
        component: () => lazyLoadView(import('@views/wallets/global-money/wallet/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/global-money/wallet/create',
        name: 'global-money.wallet.create',
        component: () => lazyLoadView(import('@views/wallets/global-money/wallet/create')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/global-money/wallet/show/:id',
        name: 'global-money.wallet.show',
        component: () => lazyLoadView(import('@views/wallets/global-money/wallet/show')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/global-money/wallet/edit/:id',
        name: 'global-money.wallet.edit',
        component: () => lazyLoadView(import('@views/wallets/global-money/wallet/edit')),
        meta: {
            authRequired: true,
        },
    },

    {
        path: '/global-money/transaction',
        name: 'global-money.transaction.index',
        component: () => lazyLoadView(import('@views/wallets/global-money/transaction/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/global-money/transaction/show/:id',
        name: 'global-money.transaction.show',
        component: () => lazyLoadView(import('@views/wallets/global-money/transaction/show')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/download-excel-shift/:id',
        name: 'download-excel-shift',
        component: () => lazyLoadView(import('@views/download-excel-shift/index')),
        meta: {
            authRequired: true,
        },
    },


    {
        path: '/easy-pay/wallet',
        name: 'easy-pay.wallet.index',
        component: () => lazyLoadView(import('@views/wallets/easy-pay/wallet/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/easy-pay/wallet/create',
        name: 'easy-pay.wallet.create',
        component: () => lazyLoadView(import('@views/wallets/easy-pay/wallet/create')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/easy-pay/wallet/show/:id',
        name: 'easy-pay.wallet.show',
        component: () => lazyLoadView(import('@views/wallets/easy-pay/wallet/show')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/easy-pay/wallet/edit/:id',
        name: 'easy-pay.wallet.edit',
        component: () => lazyLoadView(import('@views/wallets/easy-pay/wallet/edit')),
        meta: {
            authRequired: true,
        },
    },


    {
        path: '/easy-pay/transaction',
        name: 'easy-pay.transaction.index',
        component: () => lazyLoadView(import('@views/wallets/easy-pay/transaction/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/easy-pay/transaction/show/:id',
        name: 'easy-pay.transaction.show',
        component: () => lazyLoadView(import('@views/wallets/easy-pay/transaction/show')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/messages/:clientNumber',
        name: 'messages.client',
        component: () => lazyLoadView(import('@views/messages/index')),
        meta: {
            authRequired: true,
        },
    },
    {
        path: '/messages/:botNumber/:clientNumber',
        name: 'messages',
        component: () => lazyLoadView(import('@views/messages/index')),
        meta: {
            authRequired: true,
        },
    },



    // Redirect any unmatched routes to the 404 page. This may
    // require some server configuration to work in production:
    // https://router.vuejs.org/en/essentials/history-mode.html#example-server-configurations
    {
        path: '*',
        redirect: '404',
    },
];

// Lazy-loads view components, but with better UX. A loading view
// will be used if the component takes a while to load, falling
// back to a timeout view in case the page fails to load. You can
// use this component to lazy-load a route with:
//
// component: () => lazyLoadView(import('@views/my-view'))
//
// NOTE: Components loaded with this strategy DO NOT have access
// to in-component guards, such as beforeRouteEnter,
// beforeRouteUpdate, and beforeRouteLeave. You must either use
// route-level guards instead or lazy-load the component directly:
//
// component: () => import('@views/my-view')
//
function lazyLoadView(AsyncView) {
    const AsyncHandler = () => ({
        component: AsyncView,
        // A component to use while the component is loading.
        loading: require('@views/_loading').default,
        // Delay before showing the loading component.
        // Default: 200 (milliseconds).
        delay: 400,
        // A fallback component in case the timeout is exceeded
        // when loading the component.
        error: require('@views/_timeout').default,
        // Time before giving up trying to load the component.
        // Default: Infinity (milliseconds).
        timeout: 10000,
    });

    return Promise.resolve({
        functional: true,
        render(h, {data, children}) {
            // Transparently pass any props or children
            // to the view component.
            return h(AsyncHandler, data, children);
        },
    });
}
