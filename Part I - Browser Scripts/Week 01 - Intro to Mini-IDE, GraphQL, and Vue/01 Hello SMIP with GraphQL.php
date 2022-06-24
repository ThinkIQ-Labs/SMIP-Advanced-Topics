<h1>Hello SMIP</h1>

<button class="btn btn-primary w-25" onclick="getQuantities()">Get Quantities</button>

<div id="myList"></div>

<script>
    document.title = "Quantities Report";
    const getQuantities = async () =>{

        let query = `
            query q1 {
                quantities {
                    displayName
                }
            }
        `;
        
        let apiRoute = '/api/graphql/';
        let settings = { method: 'POST', headers: {} };
        let formData = new FormData();
        formData.append('query', query);
        settings.body = formData;
        let fetchQueryResponse = await fetch(apiRoute, settings);
        let data = await fetchQueryResponse.json();
        let quantities = data.data.quantities.sort((a,b)=>a>b?1:-1);
        console.log(data);

        const aList = document.createElement("ul");
        quantities.forEach(aQuantity=>{
            const aListElement = document.createElement("li");
            const textNode = document.createTextNode(aQuantity.displayName);
            aListElement.appendChild(textNode);
            aList.appendChild(aListElement);
        });
        document.getElementById("myList").appendChild(aList);

    }
</script>
