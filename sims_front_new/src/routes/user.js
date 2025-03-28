import axios from 'axios';

const getUserID = async() =>{
let account = ''
let access = ''
let data = {}

    try{
        await axios({
            method: "get",
            url: '/api/user',
        }).then(async (res1) => {
            account = res1
            await axios({
                method: "get",
                url: '/api/get-user-access/'+account.data.id,
            }).then(async (res2) => {
                access = res2
                data = {
                    account: account,
                    access: access,
                    status: 200
                }
            })
        })
    }catch(err){
        data = {
            account: '',
            access: '',
            status: 401
        }
    }

return data
}


export { getUserID }