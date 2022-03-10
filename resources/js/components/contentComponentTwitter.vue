<template>
    <div class="col-lg-12">
        <div class="justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold">Administrador de contenidos de Twitter
                            <button v-show="!editMode && !addMode" class="btn btn-sm btn-outline-primary float-right"
                                    @click="addNew">
                                Agregar nuevo
                            </button>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form">
                            <div class="col-lg-12">
                                <div class="form" v-show="!editMode && !addMode">
                                    <!--BUSCA CONTENIDO-->

                                    <!--MUESTRA LOS CONTENIDOS-->
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th>Contenido</th>
                                            <th>Descripción</th>
                                            <th>Páginas</th>
                                            <th>Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(content, i) in content_data" :key="i">
                                            <td>{{ content.name }}</td>
                                            <td>{{ content.description }}</td>
                                            <td>{{ content.pages.length }}</td>
                                            <td> <div class="list-group-item-figure">
                                                <button type="button"
                                                        class="btn btn-sm btn-icon btn-round btn-success mt-3"
                                                        @click="editContent(content)">
                                                    <i class="icon-pencil"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-icon btn-round btn-danger mt-3"
                                                        @click="deleteCategory(content)">
                                                    <i class="icon-close"></i>
                                                </button>
                                            </div></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- EDITAR CONTENIDO -->
                                <div class="form" v-if="editMode">
                                    <div class="input-group">
                                        <div class="input-group-append" title="Volver a Contenidos">
                                            <a v-show="editMode" class="btn btn-light" v-on:click="toggle">
                                                <i class="fa fa-flip-horizontal fa-share"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="content-edit">Nuevo contenido</label>
                                        <input type="text" class="form-control" v-model="category_name"
                                               id="content-edit"
                                               placeholder="Ingrese un nombre para el contenido...">
                                    </div>
                                    <div class="form-group">
                                        <label for="content-edit">Detalle contenido</label>
                                        <input type="text" class="form-control" v-model="description"
                                               placeholder="Ingrese un detalle para el contenido...">
                                    </div>
                                    <section class="card mt-4">
                                        <div class="card-title mt-3 mb-3 ml-3">
                                            <h4 class="fw-bold">Páginas de Twitter</h4>
                                        </div>
                                        <div class="list-group list-group-messages list-group-flush" id="pages_group">
                                            <div class="list-group-item read">
                                                <div class="list-group-item-body pl-3 pl-md-4">
                                                    <div class="row form-group">
                                                        <div class="col-sm-12">
                                                            <input class="form-control" type="text"
                                                                   v-model="username"
                                                                   placeholder="Nombre de usuario..."/>
                                                        </div>
                                                        <!--<div class="col-sm-5">-->
                                                            <!--<input class="form-control" type="text"-->
                                                                   <!--v-model="page_name"-->
                                                                   <!--placeholder="Ingrese el nombre de la página..."/>-->
                                                        <!--</div>-->
                                                        <!--<div class="col-sm-2">-->
                                                            <!--<input type="checkbox" value="1" id="checkbox" v-model="competence"> Competidor-->
                                                        <!--</div>-->
                                                    </div>
                                                </div>
                                                <div class="list-group-item-figure">
                                                    <button class="btn btn-sm btn-icon btn-round btn-success mt-3"
                                                            @click="addNewPage">
                                                        <i class="icon-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div v-if="pages && (pages.length > 0 && editMode)" v-for="(page, i) in pages"
                                                 :key="i"
                                                 class="list-group-item read">
                                                <div class="list-group-item-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 ">
                                                            <h6 class="list-group-item-title">
                                                                {{ page.username }}
                                                            </h6>
                                                        </div>
                                                        <div class="col-lg-6">
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
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-block btn-primary" @click="modifyCategory">
                                            Modificar
                                        </button>
                                    </div>
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
                                                <h6 for="whatsapp_group" class="fw-bold">Página de Twitter</h6>
                                            </div>
                                            <div class="list-group list-group-messages list-group-flush"
                                                 id="whatsapp_group">
                                                <div class="list-group-item unread">
                                                    <div class="list-group-item-body pl-1 pl-md-2">
                                                        <div class="row form-group">
                                                            <div class="col-sm-12">
                                                                <input class="form-control" type="text"
                                                                       v-model="username"
                                                                       placeholder="Nombre de usuario..."/>
                                                            </div>
                                                            <!--<div class="col-sm-6">-->
                                                                <!--<input class="form-control" type="text"-->
                                                                       <!--v-model="page_name" readonly="readonly"-->
                                                                       <!--placeholder="Nombre..."/>-->
                                                            <!--</div>-->
                                                            <!--<div class="col-sm-2">-->
                                                                <!--<input type="checkbox" value="1" id="checkbox" v-model="competence"> Competidor-->
                                                            <!--</div>-->

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
                                                                    {{ page.username }}
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
    name: "contentComponentTwitter",
    data() {
        return {
            editMode: false,
            addMode: false,
            current_cat: '',
            pages: [],
            category_name: '',
            description: '',
            page_name: '',
            username: '',
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
        contents: {}
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
        addPage() {
            console.log(this.username, this.username !== null);
            if(this.username){
                let datos = {},
                    username = this.username;
                datos = {username};
                console.log(datos);
                axios.post('/Twitter/get_info',datos).then(response=>{
                    if (response.data == 200) {
                        if(this.username){
                            this.pages.push({username: this.username});
                            this.username = '';
                        }
                    }
                    else{
                        Swal.fire({
                            title: "Oops",
                            text: 'No se ha encontrado la página',
                            icon: 'warning'
                        })
                    }
                });
            }
        },
        addNewPage() {
            console.log('agregar actu');
            let data={'username': this.username, 'categoria':this.current_cat};
            console.log(data);
            axios.post('/Twitter/saveScrapTwitter',data).then(response=>{
                console.log(response);
                if(response.data ===200){
                    this.pages.push({username: this.username});
                    this.username = '';
                }
                else if(response.data ===201){
                    this.username = '';
                    Swal.fire({
                        title: "Oops",
                        text: 'La página ingresada ya se encuentra asignada al contenido',
                        icon: 'warning'
                    })
                }
                else{
                    this.username = '';
                    Swal.fire({
                        title: "Oops",
                        text: 'No se ha encontrado la página.',
                        icon: 'warning'
                    })
                }
            }).catch();
        },
        removePage(i) {
            axios.post('/Twitter/destroyScrap',this.pages[i]).then(response=>{
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
                axios.post('/Twitter/store', data).then(response => {
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
            const data = new FormData(),
                category_name = this.category_name,
                description = this.description,
                category_id = this. current_cat,
                pages = JSON.stringify(this.pages)
            data.append('category_id', category_id)
            data.append('category_name', category_name)
            data.append('description', description)
            data.append('pages', pages)
            axios.post('/Twitter/update', data).then(response => {
                this.editMode = false
                const index = this.content_data.findIndex(content => {
                    return content.id === category_id;
                })
                this.content_data[index].name=category_name;
                this.content_data[index].description=category_name;
                this.clear()
            }).catch((error) => {
                Swal.fire({
                    title: "Error!",
                    text: 'Intente nuevamente',
                    icon: 'error'
                });
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
//        getContents(page) {
//            this.show = true;
//            if (typeof page == 'number') {
//                this.current_page = page;
//            }else{
//                this.current_page=1;
//            }
//            let search = '';
//            if (this.search !== '') {
//                search = `&search=${this.search}`;
//            }
////            axios.get(`/Twitter/index?Page=${this.current_page}${search}`).then(response => {
////                this.content_data = response.data
////            });
//            axios.get(`/Twitter/index`).then(response => {
//                console.log(response);
//                this.content_data = response.data
//            });
//        },
        getContents() {
            axios.get('/Twitter/syncTwitter').then(response => {
                console.log(response);
                this.content_data = response.data
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
                    axios.put(`/Twitter/${content.id}`).then(response => {
                        const index = this.content_data.findIndex(content_f => {
                            return content_f.id === content.id;
                        })
                        this.content_data.splice(index, 1);
                    }).catch();
                }
            });
        }
    },
    beforeMount() {
        this.getContents()
    }
}
</script>

<style scoped>

</style>
