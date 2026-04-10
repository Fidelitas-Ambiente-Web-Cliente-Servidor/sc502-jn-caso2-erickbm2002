$(function () {
    let formLogin = $("#formLogin");
    let btnLogout = $('#btnLogout');
    const urlBase = "index.php"

    formLogin.on("submit", function (event) {
        event.preventDefault();
        let username = $("#username");
        let password = $("#password");

        if (username.val() === "" || password.val() === "") {
            alert("Debe completar todos los campos");
        } else {
            $.post(urlBase,
                {
                    username: username.val(),
                    password: password.val(),
                    option: "login"
                },
                function (data, status) {
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.response == "00"){
                        window.location = data.rol == 'admin' ? "index.php?page=admin" : "index.php?page=talleres";
                    } else {
                        alert(data.message)
                    }
                });

        }
    })

    btnLogout.on("click", () => {
        fetch(urlBase + '?page=logout')
            .then(response => response.json())
            .then(data => {
                if (data.response == "00") return window.location = urlBase + '?page=login'
                alert(data.mensaje)
        })
    })

})
