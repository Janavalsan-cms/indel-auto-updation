document.addEventListener("DOMContentLoaded", function(){

    document.getElementById("newslettersForm")
    .addEventListener("submit", function(e){

        e.preventDefault();

        let formData = new FormData(this);

        fetch("newsletter.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {

            let msg = document.getElementById("newsletterMsg");

            if(data.trim() === "success"){
                msg.innerHTML = "<span style='color:lightgreen;'>Subscribed successfully!</span>";
                document.getElementById("newslettersForm").reset();
            }else{
                msg.innerHTML = "<span style='color:red;'>Something went wrong.</span>";
            }

        })
        .catch(() => {
            document.getElementById("newsletterMsg").innerHTML =
            "<span style='color:red;'>Server error. Try again.</span>";
        });

    });

});
