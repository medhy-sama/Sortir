import { Controller } from "@hotwired/stimulus";


    export default class extends Controller{

        static targets = [ "filter" ];



        connect(){
            console.log("Le controller est bien chargé !");
            this.filterTargets.forEach( filter => {
                filter.addEventListener ("change", () => {
                    this.submit();
                })
            })
        }

        submit() {
            const formData = new FormData(this.element);
            fetch(this.element.action, {
            method : this.element.method,
            body : formData
            }).then(response => {
                return response.text();
            }).then(html => {
                this.element.outerHTML=html;
                const url = window.location.href;
                window.history.pushState({turbolinks : true,url},"",url);
            })
        }
    }