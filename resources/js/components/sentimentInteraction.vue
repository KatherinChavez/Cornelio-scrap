<template>
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold">Sentimiento de los temas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form">
                            <div class="col-lg-12">
                                <div class="form" v-show="!editMode && !addMode">
                                    <!--BUSCA CONTENIDO-->
                                    <div class="input-group">
                                        <div class="col-md-12">
                                            <label>Temas</label>
                                            <select v-model="topics_id" class="form-control" >
                                                <option value="" selected>-- Seleccione un tema --</option>
                                                <option v-for="(topics, index) in topics" :value="topics.id" :key="index">
                                                    {{ topics.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <!--<span class="input-group-prepend">-->
                                            <button type="" class="btn btn-primary" @click="getTopics()">
                                                Obtener sentimiento
                                            </button>

                                        <div class="col-md-12 row justify-content-center" id="estadistica"></div>
                                        <!--</span>-->
                                    </div>
                                </div>
                                <div class="form" v-if="editMode">
                                    <div class="input-group">
                                        <div class="input-group-append" title="Volver a Contenidos">
                                            <a v-show="editMode" class="btn btn-light" v-on:click="toggle">
                                                <i class="fa fa-flip-horizontal fa-share"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <!--<div class="form-group">-->
                                        <!--<button class="btn btn-sm btn-block btn-primary" @click="modifyCategory">-->
                                            <!--Modificar-->
                                        <!--</button>-->
                                    <!--</div>-->
                                </div>
                            </div>

                            <!-- CREAR CONTENIDO -->
                            <div class="col-lg-12">
                                <div v-show="addMode" class="form">
                                    <a v-show="addMode" class="btn btn-light" v-on:click="toggle1">
                                        <i class="fa fa-flip-horizontal fa-share"></i>
                                    </a>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="content-create">Agregar contenido</label>
                                            <input type="text" class="form-control" v-model="category_name"
                                                   id="content-create"
                                                   placeholder="Ingrese un nombre para el contenido...">
                                        </div>
                                        <div class="form-group">
                                            <label for="content-edit">Detalle contenido</label>
                                            <input type="text" class="form-control" v-model="description"
                                                   placeholder="Ingrese un detalle para el contenido...">
                                        </div>
                                        <section class="card mt-4">
                                            <div class="card-title mt-3 mb-3 ml-3">
                                                <h6 for="whatsapp_group" class="fw-bold">Página de Facebook </h6>
                                            </div>
                                            <div class="list-group list-group-messages list-group-flush"
                                                 id="whatsapp_group">
                                                <div class="list-group-item unread">
                                                    <div class="list-group-item-body pl-1 pl-md-2">
                                                        <div class="row form-group">
                                                            <div class="col-sm-5">
                                                                <input class="form-control" type="text"
                                                                       v-model="page_id"
                                                                       placeholder="Ingrese el ID de la página..."/>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input class="form-control" type="text"
                                                                       v-model="page_name"
                                                                       placeholder="Ingrese el nombre de la página..."/>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <input type="checkbox" value="1" id="checkbox" v-model="competence"> Competidor
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="list-group-item-figure">
                                                        <button type="button"
                                                                class="btn btn-sm btn-icon btn-round btn-success mt-3"
                                                                @click="addPage">
                                                            <i class="icon-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div v-if="pages && (pages.length > 0 && addMode)" v-for="(page, i) in pages"
                                                     :key="i"
                                                     class="list-group-item unread text-center">
                                                    <div class="list-group-item-body">
                                                        <div class="row form-group">
                                                            <div class="col-sm-6">
                                                                <h6 class="list-group-item-title">
                                                                    {{ page.page_id }}
                                                                </h6>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <h6 class="list-group-item-title">
                                                                    {{ page.page_name }}
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item-figure">
                                                        <button type="button"
                                                                class="btn btn-sm btn-icon btn-round btn-danger mt-3"
                                                                @click="removePage(i)">
                                                            <i class="icon-close"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <div class="form-control form-group">
                                            <button class="btn btn-sm btn-block btn-primary" @click="save">
                                                Guardar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "sentiment-interaction",
        data() {
            return {
                editMode: false,
                addMode: false,
                current_cat: '',
                topics_id : '',
                pages: [],
                category_name: '',
                description: '',
                page_name: '',
                page_id: '',
                search: '',
                pageAccessToken:'',
                content_data:this.contents,
                competence: '',
            }
        },
        created() {

        },
        mounted() {
            this.pageAccessToken=localStorage.getItem('pageToken');
        },
        props: {
            topics: {}
        },
        methods: {
            toggle() {
                (this.editMode) ? this.editMode = false : this.editMode = true;
                this.clear()
            },
            toggle1() {
                (this.addMode) ? this.addMode = false : this.addMode = true;
                this.clear()
            },
            addNew() {
                (this.addMode) ? this.addMode = false : this.addMode = true;
            },
            infoPage() {
                if(this.pageAccessToken===''){
                    this.pageAccessToken=localStorage.getItem('pageToken');
                }
                window.FB.api(`/${this.page_id}`,
                    'GET',
                    {"fields":"name,picture,fan_count,category,about,company_overview,location,phone,emails,talking_about_count","access_token": this.pageAccessToken},
                    response => {
                        if(!response.error){
                            this.saveInformation(response );

                        }else{
                            Swal.fire({
                                title: "Oops",
                                text: 'No se ha encontrado la página',
                                icon: 'warning'
                            })
                        }
                    }

                );

            },
            saveInformation(page) {
                let datos = {},
                    fan_count = page.fan_count,
                    category = page.category,
                    page_name = page.name,
                    about = page.about,
                    company_overview = page.company_overview,
                    phone = page.phone,
                    email = page.emails,
                    talking = page.talking_about_count,
                    picture = page.picture.data.url,
                    page_id = page.id,
                    pageAccessToken = this.pageAccessToken;
                datos = {
                    page_id,
                    page_name,
                    fan_count,
                    category,
                    about,
                    company_overview,
                    phone,
                    email,
                    talking,
                    picture,
                    pageAccessToken
                },
                    axios.post('/infoPage',datos).then(response=>{
                        if (response.status != 200) {
                            //swal('Intentá de nuevo', 'El id de la página está incorrecto', 'error');
                        }
                    });
            },
            addNewPage() {
                this.infoPage();
                if(this.pageAccessToken===''){
                    this.pageAccessToken=localStorage.getItem('pageToken');
                }
                window.FB.api(`/${this.page_id}`,
                    'GET',
                    {"fields":"name,category","access_token": this.pageAccessToken},
                    response => {
                        if(!response.error){
                            this.page_id=response.id;
                            this.page_name=response.name;
                            let data={'idP': this.page_id, 'nombre': this.page_name,'competence': this.competence, 'categoria':this.current_cat}
                            axios.post('scraps/Save',data).then(response=>{
                                if(response.status===200){
                                    if(response.data==204){
                                        this.getContents();
                                        this.infoPage();
                                    }
                                    this.pages.push({page_id: this.page_id, page_name: this.page_name, competence: this.competence });
                                    this.page_id='';
                                    this.page_name='';
                                }else{
                                    Swal.fire({
                                        title: "Oops",
                                        text: 'No se ha podido guardar la página',
                                        icon: 'warning'
                                    })
                                }
                            }).catch();
                        }else{
                            Swal.fire({
                                title: "Oops",
                                text: 'No se ha encontrado la página',
                                icon: 'warning'
                            })
                        }

                    }
                );
            },
            removePage(i) {
                axios.post('scraps/delete',this.pages[i]).then(response=>{
                    if(response.data.code===200){
                        this.pages.splice(i, 1);
                    }else{
                        Swal.fire({
                            title: "Oops",
                            text: 'No ha sido posible eliminar la página',
                            icon: 'warning'
                        })
                    }
                });

            },
            save() {
                const data = new FormData(),
                    category_name = this.category_name,
                    description = this.description,
                    pages = JSON.stringify(this.pages)
                data.append('category_name', category_name)
                data.append('description', description)
                data.append('pages', pages)
                if(this.category_name && this.description){
                    axios.post('/Category/store', data).then(response => {
                        this.editMode = false
                        this.clear()
                        this.getContents();
                    }).catch(() => {
                        console.log("Error.....")
                    })
                }else{
                    Swal.fire({
                        title: "Oops",
                        text: 'Complete los campos de contenido y descripción',
                        icon: 'warning'
                    })
                    return false;
                }
            },
            modifyCategory() {
                console.log('entre');
                const data = new FormData(),
                    category_name = this.category_name,
                    description = this.description,
                    category_id = this. current_cat,
                    pages = JSON.stringify(this.pages)
                console.log(pages);
                data.append('category_id', category_id)
                data.append('category_name', category_name)
                data.append('description', description)
                data.append('pages', pages)
                axios.post('/Category/update', data).then(response => {
                    this.editMode = false
                    const index = this.content_data.findIndex(content => {
                        return content.id === category_id;
                    })
                    this.content_data[index].name=category_name;
                    this.content_data[index].description=category_name;
                    this.clear()
                }).catch((error) => {
                    console.log("Error.....")
                })
            },
            editContent(content_data) {
                this.clear()
                this.editMode = true
                this.current_cat = content_data.id
                this.category_name = content_data.name
                this.description = content_data.description
                this.pages = content_data.pages

            },
            clear() {
                this.category_name = '';
                this.description='';
                this.addMode = false
                this.editMode = false
                this.page_id = ''
                this.page_name = ''
                this.pages = []
                this.competence = ''
            },
            search_content() {
                this.getContents();
            },
            getTopics() {
                let data={'topics_id': this.topics_id}
                axios.post('/SentimentInteraction/getTopics', data).then(response => {
                    let comments = response.data.Comment;
                    let posts = response.data.Post;

                    posts.forEach(function (post, index) {
                        console.log(post);
                        $('#estadistica').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="post_sentiment"></div>');
                        Highcharts.chart("post_sentiment", {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },

                            title: {
                                text: 'Sentimiento de conversación <br>del tema  <b>selecionado</b> <br>en las Últimas <b>24 horas</b>',

                            },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                    }
                                }

                            },

                            colors: ['#317f43', '#CB3234', '#ffff00'],
                            series: [{
                                type: 'pie',
                                name: 'Porcentaje de Reacciones',
                                innerSize: '50%',
                                data: [
                                    ['Positivos ' , post.positivo],
                                    ['Negativos ' , post.negativo],
                                    ['Neutrales ' , post.neutral],
                                ]
                            }]
                        });
                    });

                    comments.forEach(function (comment, index) {
                        $('#estadistica').append('<div class="col-md-6 mt-5 b-b1 pb-2 mb-4" id="comment_sentiment"></div>');
                        Highcharts.chart("comment_sentiment", {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },

                            title: {
                                text: 'Sentimiento de conversación <br>del tema  <b>selecionado</b> <br>en las Últimas <b>24 horas</b>',

                            },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                    }
                                }

                            },

                            colors: ['#317f43', '#CB3234', '#ffff00'],
                            series: [{
                                type: 'pie',
                                name: 'Porcentaje de Reacciones',
                                innerSize: '50%',
                                data: [
                                    ['Positivos ' , comment.positivo],
                                    ['Negativos ' , comment.negativo],
                                    ['Neutrales ' , comment.neutral],
                                ]
                            }]



                        });
                    });

                }).catch((error) => {
                    console.log(error);
                    console.log("Error.....")
                })
            },
            deleteCategory(content){
                swal({
                    title: "¿Esta seguro?",
                    text: "una vez eliminado el contenido no podrá recuperarlo",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then( response=> {
                    if (response) {
                        axios.put(`Category/${content.id}`).then(response => {
                            const index = this.content_data.findIndex(content_f => {
                                return content_f.id === content.id;
                            })
                            this.content_data.splice(index, 1);
                        }).catch();
                    }
                });
            }
        },
    }
</script>

<style scoped>

</style>
