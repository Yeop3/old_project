import axios from "axios";

export function checkAccessGlobalMoneyWallet({login, password, type}){
    //console.log(login,password,type);
    return axios.post(`api/global-money/wallet/access`, {login, password, type})
        .then((res) => {
            return res.data;
        });
}
