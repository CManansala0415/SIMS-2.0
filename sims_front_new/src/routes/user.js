import axios from 'axios';

const getUserID = async() =>{
let user = ''
    try{
        await axios({
            method: "get",
            url: '/api/user',
        }).then(async (results) => {
            user = results
        })
    }catch(err){
        user = 401
    }
return user
}


export { getUserID }