async function getData(){
    try{
        let response = await fetch('http://test.loc:8080/users/1/services/1/tarifs',{
            headers: new Headers({     
                'Content-Type': 'application/json; charset=utf-8'
                })
                })
            let data = await response.json()    
  
            console.log(data) ;               
        }catch(e){
        console.error(error)
        }
    }

    async function putData(someData){
        const putMethod = {
            method: 'PUT', // Method itself
            headers: {
             'Content-type':'application/json; charset=UTF-8' // Indicates the content 
            },
            body: JSON.stringify(someData) // We send data in JSON format
           }
           try{
               let response = await fetch('http://test.loc:8080/users/1/services/1/tarif', putMethod)
               let data = await response.json();
               console.log(data) // Manipulate the data retrieved back, if we want to do something with it

           } catch(e){
            console.log(err)
           }
         
        }
        putData({
            'tarif_id':1
        })

  
  
