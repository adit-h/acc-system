/*
    Custom javascript
*/

function submitRolePermission(routeUrl) {
    // get token
    let token = $("input[name=_token]").val();
    // get All checked value by class name
    var cb = $(".form-check-input");
    var val = cb
        .filter(":checked")
        .map(function () {
            var res = this.id;
            return res;
        })
        .get();
    console.log(val.length);
    // break down the id then store role id & permission id to array
    let arr = new Array();
    for (i = 0; i < val.length; i++) {
        let raw = val[i].split("-");
        arr[i] = [raw[1], raw[3]];
    }
    //console.log(arr);
    let config = [{
        url: routeUrl,
        data: arr
    }];
    //console.log(config);

    //Csing Axios
    axios.post(routeUrl, {
        data: arr
    })
    .then(function (response) {
        console.log(response);

        //location.reload();
    })
    .catch(function (error) {
        console.log(error);
    });
}
