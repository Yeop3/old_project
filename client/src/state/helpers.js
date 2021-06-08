import {mapState, mapGetters, mapActions} from 'vuex';

export const authComputed = {
    ...mapState('auth', {
        currentUser: (state) => state.currentUser,
        typeSelect: (state) => state.type
    }),
    ...mapGetters('auth', ['loggedIn']),
};

export const driversComputed = {
    ...mapState('drivers', {
        drivers: (state) => state.drivers,
        driver: (state) => state.driver,
        driver_permissions: (state) => state.driver_permissions,
        drivers_select: (state) => state.drivers_select,
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('drivers', ['checkDriver', 'getDrivers']),
};

export const discountsComputed = {
    ...mapState('discounts', {
        discount: (state) => state.discount,
        discounts: (state) => state.discounts,
        page: (state) => state.page,
        spiner: (state) => state.spiner,

    }),
    ...mapGetters('discounts', ['checkDiscount', 'getDiscounts', 'getCountProducts']),
};

export const shiftsComputed = {
    ...mapState('shifts', {
        shift: (state) => state.shift,
        shifts: (state) => state.shifts,
        shifts_spiner: (state) => state.shifts_spiner,
    }),
    ...mapGetters('shifts', ['checkShift', 'getShifts']),
};

export const productTypesComputed = {
    ...mapState('productTypes', {
        product_types: (state) => state.product_types,
        product_type: (state) => state.product_type,
        // page: (state) => state.page,
        product_types_select: (state) => state.product_types_select,
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('productTypes', ['checkProductTypes', 'getProductTypes']),
};

export const locationsComputed = {
    ...mapState('location', {
        location: (state) => state.location,
        locations: (state) => state.locations,
        page: (state) => state.page,
        locations_select: (state) => state.locations_select,
        locations_root_select: (state) => state.locations_select.filter(l => l.depth === 0),
        locations_child_select: (state) => state.locations_select.filter(l => l.depth !== 0),
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('location', ['checkLocations', 'getLocations', 'getLocationsSelect',]),
};

export const commissionComputed = {
    ...mapState('commission', {
        commission_types: (state) => state.commission_types,
    }),
    ...mapGetters('commission', []),
};

export const unitComputed = {
    ...mapState('unit', {
        unit_types: (state) => state.unit_types,
    }),
    ...mapGetters('unit', ['getUnitType']),
};

export const authMethods = mapActions('auth', ['logIn', 'logOut', 'register', 'resetPassword', 'getSelectType']);

export const driversMethods = mapActions('drivers', ['create', 'getIndex', 'edit', 'deleteById', 'getSelectDriver', 'getById', 'loadPermissions']);

export const botsMethods = mapActions('bots', ['loadAll', 'load', 'create', 'getWebhookInfo', 'update', 'delete', 'reinstallWebhook']);

export const locationsMethods = mapActions('location', ['create', 'getIndex', 'getById', 'edit', 'deleteById', 'getSelect']);

export const discountsMethods = mapActions('discounts', ['create', 'getIndex', 'getById', 'edit', 'deleteById', 'getById']);

export const shiftsMethods = mapActions('shifts', ['getIndex', 'loadShift', 'exportExcelByID']);

export const botsComputed = {
    ...mapState('bots', {
        bot: (state) => state.bot,
        bot_webhookinfo: (state) => state.bot_webhookinfo,
        bots: (state) => state.bots,
        page: (state) => state.page,
        pagesCount: (state) => state.pagesCount,
        total: (state) => state.total,
        perPage: (state) => state.perPage,
        select_bots: (state) => state.select_bots,
        spiner: (state) => state.spiner,
    }),
};


export const botLogicsMethods = mapActions('botLogics', ['loadBotLogics', 'loadBotLogic', 'cloneBotLogic', 'deleteBotLogic', 'updateBotLogic']);
export const botLogicsComputed = {
    ...mapState('botLogics', {
        botLogic: (state) => state.botLogic,
        botLogics: (state) => state.botLogics,
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('botLogics', ['botLogicsForSelect']),
};

export const productTypesMethods = mapActions('productTypes', ['create', 'getIndex', 'getById', 'edit', 'deleteById', 'getSelectProductType',]);

export const productComputed = {
    ...mapState('product', {
        product: (state) => state.product,
        products: (state) => state.products,
        // page: (state) => state.page,
        status_list: (state) => state.status_list,
        spiner_product: (state) => state.spiner_product
    }),
    ...mapGetters('product', ['checkProduct']),
};

export const productMethods = mapActions('product', ['create', 'getIndex', 'getById', 'edit', 'deleteById', 'actionsSelect', 'createMany', 'getById']);

export const clientsComputed = {
    ...mapState('clients', {
        client: (state) => state.client,
        clients: (state) => state.clients,
        clients_for_select: (state) => state.clients_for_select,
        client_spam_reserve: (state) => state.client_spam_reserve,
        spiner: (state) => state.spiner,
    })
};
export const clientsMethods = mapActions('clients', [
    'loadClients', 'loadClientsForSelect',  'loadClient', 'updateClient',
    'deleteClient', 'banClient', 'unBanClient',
    'blackListClient', 'unBlackListClient',
    'exportTelegramCsv', 'unbanAll',
    'multiBan', 'multiBlackList', 'loadSpamReserve', 'multiDelete', 'sendMessageToClient']);

export const operatorMethods = mapActions('operator', ['create', 'getIndex', 'edit', 'deleteById', 'loadForBots']);
export const operatorForBotsMethods = mapActions('operator', {
    loadOperatorsForBots: 'loadForBots'
});

export const operatorComputed = {
    ...mapState('operator', {
        operators: (state) => state.operators,
        operator: (state) => state.operator,
        operators_select: (state) => state.operators_select,
        operators_for_bots: (state) => state.operators_for_bots.map(operator => {
            return {
                ...operator,
                text: operator.client ? `${operator.text}(${operator.client.telegram_id})` : `${operator.text}(не назначен клиент)`,
                disabled: !operator.client,
            };
        }),
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('operator', []),
};

export const qiwiManualMethods = mapActions('qiwiManual', ['create', 'getIndex', 'edit', 'deleteById', 'deleteForeverById']);

export const qiwiManualomputed = {
    ...mapState('qiwiManual', {
        qiwiManual: (state) => state.qiwiManual,
        qiwiManuals: (state) => state.qiwiManuals,
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('qiwiManual', []),
};

export const qiwiManualDeletedMethods = mapActions('qiwiManual', [
    'getIndexDeleted', 'deleteByIdDeleted', 'restoreQiwiById', 'qiwiDeletedClear'
]);

export const qiwiManualDeletedComputed = {
    ...mapState('qiwiManual', {
        qiwiManualsDeleted: (state) => state.qiwiManualsDeleted,
        qiwiManualDeleted: (state) => state.qiwiManualDeleted,
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('qiwiManual', []),
};

export const paymentQiwiManualDeletedMethods = mapActions('payments/qiwiManual', [
    'create', 'getIndex', 'edit', 'deleteById',
]);

export const paymentqiwiManualDeletedComputed = {
    ...mapState('payments/qiwiManual', {
        qiwiManuals: (state) => state.qiwiManuals,
        qiwiManual: (state) => state.qiwiManual,
        qiwiManualPhone: (state) => state.qiwiManualPhone,
        spiner: (state) => state.spiner,
    }),
    ...mapGetters('payments/qiwiManual', []),
};

export const orderMethods = mapActions('order', [
    'getIndex',
    'getStatus',
    'setStatusGive',
    'setTransferStatus',
    'setCancelOperatorStatus',
    'setCountFilterStatus',
    'setCountFilter',
    'setCancelAll',
    'setCancelAwaiting',
    'setCancelPartially',
    'getCrypto',
    'getById',
    'restorationCanceledOrder',
    'restorationPaidOrder'
]);

export const orderComputed = {
    ...mapState('order', {
        order_select: (state) => state.order_select,
        orders: (state) => state.orders,
        order_status: (state) => state.order_status,
        orderCounterFilter: (state) => state.orderCounterFilter,
        orderCounterFilterStatus: (state) => state.orderCounterFilterStatus,
        crypto: (state) => state.crypto,
        spiner_table: (state) => state.spiner_table,
        order: (state) => state.order
    }),
    ...mapGetters('order', []),
};

export const proxyMethods = mapActions('proxy', ['loadProxies', 'loadProxy', 'createProxy', 'updateProxy', 'deleteProxy']);

export const proxyComputed = {
    ...mapState('proxy', {
        proxy: (state) => state.proxy,
        proxies: (state) => state.proxies,
        spiner: (state) => state.spiner,
    })
};

export const statisticMethods = mapActions('statistic', ['loadStatistic', 'loadStatusStatistic', 'chartStatistic']);
export const statisticComputed = {
    ...mapState('statistic', {
        statistic: (state) => state.statistic,
        statusStatistic: (state) => state.statusStatistic,
        chart: (state) => state.chart,
        spiner: (state) => state.spiner,
    })
};

export const dispatcherMethods = mapActions('dispatcher', ['create', 'getIndex']);
export const dispatcherComputed = {
    ...mapState('dispatcher', {
        dispatcher: (state) => state.dispatcher,
        dispatchers: (state) => state.dispatchers,
        spiner_dispatchers: (state) => state.spiner,
    })
};

export const cryptoWalletMethods = mapActions('wallet/crypto', ['create', 'getIndex', 'deleteCrypto', 'getCrypt', 'update']);
export const cryptoWalletComputed = {
    ...mapState('wallet/crypto', {
        crypto: (state) => state.crypto,
        crypt: (state) => state.crypt,
        spiner_crypto: (state) => state.spiner,
    })
};
export const cryptoTransactionMethods = mapActions('transaction/crypto', ['create', 'getIndex', 'deleteCrypto', 'getBitap', 'update']);
export const cryptoTransactionComputed = {
    ...mapState('transaction/crypto', {
        crypto: (state) => state.crypto,
        crypt: (state) => state.crypt,
        spiner_crypto: (state) => state.spiner,
    })
};

export const GlobalMoneyWalletMethods = mapActions('wallet/globalMoney', [
    'loadGlobalMoneyWallet',
    'deleteGlobalMoneyWallet',
    'create',
    'getGlobalMoneyWallet',
    'update',
    'getGlobalMoneyWalletSelect',
]);

export const GlobalMoneyWalletComputed = {
    ...mapState('wallet/globalMoney', {
        globalMoneyWallets: (state) => state.globalMoneyWallets,
        globalMoneyWallet: (state) => state.globalMoneyWallet,
        global_money_wallet_spinner: (state) => state.spiner,
        type_globalMoney: (state) => state.type,
        globalMoneyWalletSelect: (state) => state.globalMoneyWalletSelect,
        accessGlobalMoneyWallet: (state) => state.accessGlobalMoneyWallet
    })
};

export const EasyPayWalletMethods = mapActions('wallet/easyPay', [
    'loadEasyPayWallet',
    'deleteEasyPayWallet',
    'create',
    'getEasyPayWallet',
    'update',
    'getEasyPayWalletSelect',
    'checkAccount',
    'restoreBalanceWallet'
]);

export const EasyPayWalletComputed = {
    ...mapState('wallet/easyPay', {
        easyPayWallets: (state) => state.easyPayWallets,
        easyPayWallet: (state) => state.easyPayWallet,
        easy_pay_wallet_spinner: (state) => state.spiner,
        easyPayWalletSelect: (state) => state.easyPayWalletSelect
    })
};

export const GlobalMoneyTransactionMethods = mapActions('transaction/globalMoney', [
    'loadGlobalMoneyTransaction',
    'getGlobalMoneyTransaction',
]);

export const GlobalMoneyTransactionComputed = {
    ...mapState('transaction/globalMoney', {
        globalMoneyTransactions: (state) => state.globalMoneyTransactions,
        globalMoneyTransaction: (state) => state.globalMoneyTransaction,
        globalMoneyTransactionSpinner: (state) => state.spiner,
    })
};

export const EasyPayTransactionMethods = mapActions('transaction/easyPay', [
    'loadEasyPayTransaction',
    'getEasyPayTransaction',
]);

export const EasyPayTransactionComputed = {
    ...mapState('transaction/easyPay', {
        easyPayTransactions: (state) => state.easyPayTransactions,
        easyPayTransaction: (state) => state.easyPayTransaction,
        easyPayTransactionSpinner: (state) => state.spiner,
    })
};

export const clientsActualDispatchComputed = {
    ...mapState('clients', {
        clientActuals: (state) => state.clientActuals,
        spiner_actual: (state) => state.spiner,
    })
};
export const clientsActualDispatchMethods = mapActions('clients', [
    'loadHandDispatchActualTelegram',
    'downloadUsernameActual',
]);

export const paymentMethodComputed = {
    ...mapState('paymentMethod', {
        paymentMethods: (state) => state.paymentMethods,
    }),
};

export const paymentMethods = mapActions('paymentMethod', ['loadPaymentMethods']);

export const stokerMethods = mapActions('stokers', ['loadStokers','createStoker','loadStoker','deleteStoker','updateStoker']);

export const stokerComputed = {
    ...mapState('stokers', {
        stokers: (state) => state.stokers,
        stoker: (state) => state.stoker,
        spiner_stoker: (state) => state.spiner_stoker
    })
};

export function getFilters(key) {
    return JSON.parse(localStorage.getItem(key) || '{}');
}

export function saveFilters(key, filters) {
    return localStorage.setItem(key, JSON.stringify(filters));
}

export function getHumanDate(timestamp) {
    const d = timestamp ? new Date(timestamp) : new Date();
    return d.toLocaleDateString();
}

export async function modalConfirm(text) {
    return await this.$bvModal.msgBoxConfirm(text, {
        title: 'Пожалуйста подтвердите',
        size: 'sm',
        buttonSize: 'sm',
        okVariant: 'danger',
        okTitle: 'Удалить',
        cancelTitle: 'Отмена',
        footerClass: 'p-2',
        hideHeaderClose: false,
        centered: true
      });
}