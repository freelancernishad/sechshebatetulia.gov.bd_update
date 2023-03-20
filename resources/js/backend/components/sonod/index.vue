<template>
    <div>
        <loader v-if="preLooding" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40"
            objectbg="#999793" opacity="80" name="circular"></loader>
        <div class="breadcrumbs-area">
            <h3>আবেদনের তালিকা</h3>
            <ul>
                <li>
                    <router-link :to="{ name: 'Dashboard' }">Home</router-link>
                </li>
                <li>আবেদনের তালিকা</li>
            </ul>
        </div>



        <div class="card">
            <div class="card-header">
                 <h3>খুঁজুন</h3>
                 <form @submit.stop.prevent="searchSondId">
                <div class="form-group d-flex" style="width:300px">
                    <input type="text" class="form-control" v-model="sonod_id" >

                    <button type="button" disabled class="btn btn-info" v-if="searching" style="font-size: 22px;margin-left: 11px;">অপেক্ষা করুন</button>
                    <button type="submit" class="btn btn-info" v-else style="font-size: 22px;margin-left: 11px;">খুঁজুন</button>

                </div>
            </form>


                <nav aria-label="Page navigation example" v-if="TotalRows>20">
            <ul class="pagination  justify-content-end">
                <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li> -->
                <li class="page-item" v-for="(pag,index) in Totalpage" :key="'p'+index" v-if="index==0 && pag.url">
                    <router-link class="page-link"
                        :to="{name:'applicationlist',params:{type:$route.params.type},query:{page:pag.url.split('?')[1].split('=')[1]}}"
                        v-html="pag.label"></router-link>
                </li>
                <li class="page-item" v-for="(pag,index) in Totalpage" :key="'i'+index"
                    :class="{active:pag.active,'disabled':pag.label=='...'}"
                    v-if="index!=0 && pag.label!='Next &raquo;'">
                    <router-link class="page-link"
                        :to="{name:'applicationlist',params:{type:$route.params.type},query:{page:pag.label}}"
                        v-html="pag.label"></router-link>
                </li>
                <li class="page-item" v-for="(pag,index) in Totalpage" :key="'l'+index"
                    v-if="pag.label=='Next &raquo;'  && pag.url">
                    <router-link class="page-link"
                        :to="{name:'applicationlist',params:{type:$route.params.type},query:{page:pag.url.split('?')[1].split('=')[1]}}"
                        v-html="pag.label"></router-link>
                </li>
                <!-- <li class="page-item"><a class="page-link" href="#">Next</a></li> -->
            </ul>
        </nav>

            </div>

            <div class="card-body">
                <table class="table table-hover table-striped sonodTable">
                    <thead class="sonodThead">


                        <tr>

                            <th width="8%" data-orderable="false" v-if="$route.params.type=='approved'" >লাইসেন্স নং</th>
                            <th width="8%" data-orderable="false" v-else >রেজিস্টেশন নং</th>

                            <th width="20%" >নাম</th>
                            <th width="15%" >মোবাইল নম্বর</th>
                            <th width="25%" >ঠিকানা</th>
                            <th width="15%" >View</th>
                            <th width="40%" >Action</th>
                        </tr>


                    </thead>
                    <tbody class="sonodTbody">

                        <tr class="sonodTr" v-for="(item,index) in items" :key="item.id">

                            <td class="sonodTd">{{ item.licence_no }}</td>

                            <td class="sonodTd" v-if="item.applicant_type=='একক ব্যক্তি'">{{ item.appicant_name }}</td>
                            <td class="sonodTd" v-else-if="item.applicant_type=='সমবায় সমিতি/প্রতিষ্ঠান'">{{ item.gostir_name }}</td>
                            <td class="sonodTd" v-else-if="item.applicant_type=='একটি গোষ্ঠী'">{{ item.appicant_sumiti_name }}</td>
                            <td class="sonodTd">{{ item.mobile_number }}</td>
                            <td class="sonodTd">{{ item.district }},{{ item.upozila }},{{ item.upozila }},{{ item.upozila }},{{ item.post }}</td>



                            <td class="sonodTd">

                                <a  v-if="item.status=='processied'" target="_blank" :href="'/dashboard/application/report/'+item.id" class="btn btn-success  mt-3">Report Download</a>

                                <span  v-else class="btn btn-info mt-3" @click="info(item, index, $event.target)">View</span>

                                <span class="btn btn-info mt-3" @click="filesView(item, index, $event.target)">কাগজপত্র</span>
                                <a class="btn btn-info  mt-3" target="_blank" :href="'/applicantion/full/copy/'+item.id">ডাউনলোড</a>

                            </td>





                            <td class="sonodTd">

                                <a v-if="item.status=='pending'" :href="'/dashboard/application/edit/'+item.id" class="btn btn-info">Edit</a>

                                <span v-if="item.status=='pending'" @click="approve('/api/application/status', item, 'processing','আবেদনটি তদন্তের জন্য প্রেরণ করতে চান!','আবেদনটি তদন্তের জন্য প্রেরণ করা হয়েছে!', $event.target)" class="btn btn-success">তদন্তের জন্য প্রেরণ করুন</span>

                                 <span  v-if="item.status=='processing' && $localStorage.getItem('position') == 'admin'" @click="approve('/api/application/status', item, 'processied','আবেদনটির প্রতিবেদন দাখিল করতে চান!','আবেদনটির প্রতিবেদন দাখিল করা হয়েছে!', $event.target,'modal')" class="btn btn-success">প্রতিবেদন দাখিল করুন</span>




                                <span v-if="item.status=='processied'"  @click="approve('/api/application/status', item, 'approved','আবেদনটি অনুমোদন করতে চান!','আবেদনটি অনুমদিত হয়েছে!', $event.target)"  class="btn  btn-success">অনুমোদন</span>
                                <span v-if="item.status=='processied'"  @click="approve('/api/application/status', item, 'canceled','আবেদনটি বাতিল করতে চান!','আবেদনটি বাতিল হয়েছে!', $event.target)"  class="btn btn-warning">বাতিল</span>



                                    <a  v-if="item.status=='approved'" :href="'/license/'+item.id" target="_blank" class="btn btn-success">লাইসেন্সে ডাউনলোড করুন</a>

                                    <span v-if="item.payment_status=='Unpaid'" class="btn btn-danger">Unpaid</span>
                                    <span v-else-if="item.payment_status=='Paid'"  class="btn btn-success">Paid</span>
                                    <!-- <a  v-if="item.status=='approved'" :href="'/l/f/'+item.id+'?f=l'" target="_blank" class="btn btn-success">Test pay</a> -->


                                <a  v-if="item.status=='canceled'" :href="'/dashboard/application/delete/'+item.id" class="btn btn-danger">Delete</a>



                            </td>
                        </tr>

                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
                <!-- <approve-component></approve-component> -->
            </div>
            <div class="card-footer">

                <nav aria-label="Page navigation example" v-if="TotalRows>20">
            <ul class="pagination  justify-content-end">
                <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li> -->
                <li class="page-item" v-for="(pag,index) in Totalpage" :key="'p'+index" v-if="index==0 && pag.url">
                    <router-link class="page-link"
                        :to="{name:'applicationlist',params:{type:$route.params.type},query:{page:pag.url.split('?')[1].split('=')[1]}}"
                        v-html="pag.label"></router-link>
                </li>
                <li class="page-item" v-for="(pag,index) in Totalpage" :key="'i'+index"
                    :class="{active:pag.active,'disabled':pag.label=='...'}"
                    v-if="index!=0 && pag.label!='Next &raquo;'">
                    <router-link class="page-link"
                        :to="{name:'applicationlist',params:{type:$route.params.type},query:{page:pag.label}}"
                        v-html="pag.label"></router-link>
                </li>
                <li class="page-item" v-for="(pag,index) in Totalpage" :key="'l'+index"
                    v-if="pag.label=='Next &raquo;'  && pag.url">
                    <router-link class="page-link"
                        :to="{name:'applicationlist',params:{type:$route.params.type},query:{page:pag.url.split('?')[1].split('=')[1]}}"
                        v-html="pag.label"></router-link>
                </li>
                <!-- <li class="page-item"><a class="page-link" href="#">Next</a></li> -->
            </ul>
        </nav>
            </div>
        </div>




        <!-- Info modal -->
        <b-modal :id="infoModal.id" size="xl" >
            <div class="row">
      <div class="col-12">



        <div class="card">
          <div class="card-body">
              <div class="d-flex justify-content-between mb-3">
            <h5 class="card-title"></h5>
            </div>

            <div class="row">

                <div class="col-md-12 form_title">
                    <h5>আবেদনকারীর নামঃ</h5>
                </div>





                <div class="col-md-4 col-6 mt-3"><b>আবেদনকারীর ধরণ : </b>{{ infoModal.content.applicant_type }}</div>

                <div v-if="infoModal.content.applicant_type=='একক ব্যক্তি'" class="col-md-4 col-6 mt-3"><b>নাম : </b>{{ infoModal.content.appicant_name }}</div>
                <div v-if="infoModal.content.applicant_type=='একক ব্যক্তি'" class="col-md-4 col-6 mt-3"><b>পিতার নাম : </b>{{ infoModal.content.applicant_father_name }}</div>


                <div v-if="infoModal.content.applicant_type=='সমবায় সমিতি/প্রতিষ্ঠান'" class="col-md-4 col-6 mt-3"><b>সমিতি/প্রতিষ্ঠানের নাম : </b>{{ infoModal.content.appicant_sumiti_name }}</div>
                <div v-if="infoModal.content.applicant_type=='সমবায় সমিতি/প্রতিষ্ঠান'" class="col-md-4 col-6 mt-3"><b>রেজিস্ট্রেশন নম্বর : </b>{{ infoModal.content.applicant_sumiti_registration_no }}</div>
                <div v-if="infoModal.content.applicant_type=='সমবায় সমিতি/প্রতিষ্ঠান'" class="col-md-4 col-6 mt-3"><b>আবেদন করিবার জন্য ক্ষমতাপ্রাপ্ত প্রতিনিধির নাম : </b>{{ infoModal.content.applicant_p_m_name }}</div>


                <div v-if="infoModal.content.applicant_type=='একটি গোষ্ঠী'" class="col-md-4 col-6 mt-3"><b>গোষ্ঠীর নাম : </b>{{ infoModal.content.gostir_name }}</div>
                <div v-if="infoModal.content.applicant_type=='একটি গোষ্ঠী'" class="col-md-4 col-6 mt-3"><b>আবেদন করিবার জন্য ক্ষমতাপ্রাপ্ত প্রতিনিধির নাম : </b>{{ infoModal.content.applicant_g_p_m_name }}</div>




                <div class="col-md-12 form_title">
                    <h5>আবেদনকারীর ঠিকানা</h5>
                </div>












                <div class="col-md-4 col-6 mt-3"><b>জেলা : </b>{{ infoModal.content.district }}</div>
                <div class="col-md-4 col-6 mt-3"><b>উপজেলা : </b>{{ infoModal.content.upozila }}</div>
                <div class="col-md-4 col-6 mt-3"><b>ইউনিয়ন : </b>{{ infoModal.content.union }}</div>
                <div class="col-md-4 col-6 mt-3"><b>ডাকঘর : </b>{{ infoModal.content.post }}</div>
                <div class="col-md-4 col-6 mt-3"><b>ওয়ার্ড নং : </b>{{ infoModal.content.wordNo }}</div>
                <div class="col-md-4 col-6 mt-3"><b>গ্রাম : </b>{{ infoModal.content.village }}</div>
                <div class="col-md-4 col-6 mt-3"><b>মোবাইল নম্বর : </b>{{ infoModal.content.mobile_number }}</div>
                <div class="col-md-4 col-6 mt-3"><b>জাতীয় পরিচয় নং : </b>{{ infoModal.content.nid_no }}</div>
                <div class="col-md-4 col-6 mt-3"><b>প্রার্থীত নলকূপের শ্রেণি : </b>{{ infoModal.content.nolkup_type }}</div>
                <div class="col-md-4 col-6 mt-3"><b>প্রার্থীত নলকূপের সাইজ : </b>{{ infoModal.content.nolkup_size }}</div>




                <div class="col-md-12 form_title">
                    <h5>নলকূপের দ্বারা সম্ভাব্য উপকৃত এলাকা এবং তথ্যাদি</h5>
                </div>


                <div class="col-md-12 col-12 mt-3">{{ infoModal.content.area_description }}</div>



                <div class="col-md-12 form_title">
                    <h5>নলকূপ স্থাপনের প্রস্তাবিত স্থান</h5>
                </div>


                <div class="col-md-4 col-6 mt-3"><b>এলাকার নাম : </b>{{ infoModal.content.area_name }}</div>
                <div class="col-md-4 col-6 mt-3"><b>মৌজার নাম : </b>{{ infoModal.content.mouja_name }}</div>
                <div class="col-md-4 col-6 mt-3"><b>জে.এল নং : </b>{{ infoModal.content.JL_No }}</div>
                <div class="col-md-4 col-6 mt-3"><b>খতিয়ান নং : </b>{{ infoModal.content.khotiyan_no }}</div>
                <div class="col-md-4 col-6 mt-3"><b>দাগ নং : </b>{{ infoModal.content.dag_NO }}</div>
                <div class="col-md-4 col-6 mt-3"><b>চাষাবাদকৃত জমির পরিমাণ : </b>{{ infoModal.content.land_amount }}</div>




                <div class="col-md-12 form_title">
                    <h5>প্রস্তাবিত স্থান হতে নিকটবর্তী নলকূপের দূরত্ব</h5>
                </div>



                <div class="col-md-4 col-6 mt-3"><b>নিকটবর্তী নলকূপের শ্রেণি : </b>{{ infoModal.content.near_nolkup_type }}</div>
                <div class="col-md-4 col-6 mt-3"><b>উত্তর দিকে (মিটার ) : </b>{{ infoModal.content.near_nolkup_uttor }}</div>
                <div class="col-md-4 col-6 mt-3"><b>দক্ষিণ দিকে (মিটার ) : </b>{{ infoModal.content.near_nolkup_dokkhin }}</div>
                <div class="col-md-4 col-6 mt-3"><b>পূর্ব দিকে (মিটার ) : </b>{{ infoModal.content.near_nolkup_purbo }}</div>
                <div class="col-md-4 col-6 mt-3"><b>পশ্চিম দিকে (মিটার ) : </b>{{ infoModal.content.near_nolkup_poscim }}</div>
                <div class="col-md-4 col-6 mt-3"><b>বৈদ্যুতিক সংযোগের দূরত্ব (মিটার ) : </b>{{ infoModal.content.electricity_distance }}</div>
                <div class="col-md-12 col-12 mt-3"><b>লাইসেন্স প্রদানের জন্য কোনো বিশেষ বিবেচনা : </b>{{ infoModal.content.description }}</div>
                <div class="col-md-4 col-6 mt-3"><b>জমাকৃত ফি এর পরিমাণ : </b>{{ infoModal.content.deposite_fee }}</div>
                <div class="col-md-4 col-6 mt-3"><b>জমাদানের তারিখ : </b>{{ infoModal.content.deposite_date }}</div>
                <div class="col-md-4 col-6 mt-3"><b>নলকূপ স্থাপনের জন্য প্রস্তাবিত স্থান/জমির মালিকানার ধরণ : </b>{{ infoModal.content.owner_type }}</div>

                </div>
            </div>
            </div>
            </div>
            </div>



            <template #modal-footer>
<div></div>
      </template>

        </b-modal>



        <!-- Info modal -->
        <b-modal :id="filesModal.id" size="xl" >

            <div class="row">




                <div class="col-md-12 form_title">
                    <h5>আবেদনকারীর পাসপোর্ট সাইজের ছবি</h5>
                </div>

                <div class="col-md-12 text-center">

                    <img width="250px" height="300px" :src="$asseturl+filesModal.content.passport_size_mage" alt="">

                    <br/>
                    <a target="_blank" style="padding: 6px 23px;font-size: 29px;" :href="$asseturl+filesModal.content.passport_size_mage" class="btn btn-info text-center">ডাউনলোড</a>
                </div>



                <div class="col-md-12 form_title">
                    <h5>জাতীয় পরিচয়পত্রের কপি</h5>
                </div>

                <div class="col-md-12 text-center">
                    <iframe     width="100%" height="300px"  :src="$asseturl+filesModal.content.nid_copy" frameborder="0"></iframe> <br/>
                    <a target="_blank" style="padding: 6px 23px;font-size: 29px;" :href="$asseturl+filesModal.content.nid_copy" class="btn btn-info text-center">ডাউনলোড</a>
                </div>



                <div class="col-md-12 form_title">
                    <h5>দলিলের কপি</h5>
                </div>

                <div class="col-md-12 text-center">
                    <iframe     width="100%" height="300px"  :src="$asseturl+filesModal.content.land_copy" frameborder="0"></iframe> <br/>
                    <a target="_blank" style="padding: 6px 23px;font-size: 29px;" :href="$asseturl+filesModal.content.land_copy" class="btn btn-info text-center">ডাউনলোড</a>
                </div>



                <div class="col-md-12 form_title">
                    <h5>খতিয়ানের কপি</h5>
                </div>

                <div class="col-md-12 text-center">
                    <iframe     width="100%" height="300px"  :src="$asseturl+filesModal.content.khotiyan_copy" frameborder="0"></iframe> <br/>
                    <a target="_blank" style="padding: 6px 23px;font-size: 29px;" :href="$asseturl+filesModal.content.khotiyan_copy" class="btn btn-info text-center">ডাউনলোড</a>
                </div>



                <div class="col-md-12 form_title">
                    <h5>ভূমি উন্নয়ন কর পরিশোধের কপি</h5>
                </div>

                <div class="col-md-12 text-center">
                    <iframe     width="100%" height="300px"  :src="$asseturl+filesModal.content.tax_copy" frameborder="0"></iframe> <br/>
                    <a target="_blank" style="padding: 6px 23px;font-size: 29px;" :href="$asseturl+filesModal.content.tax_copy" class="btn btn-info text-center">ডাউনলোড</a>
                </div>



                <div class="col-md-12 form_title">
                    <h5>নকশা/মৌজা ম্যাপ</h5>
                </div>

                <div class="col-md-12 text-center">
                    <iframe     width="100%" height="300px"  :src="$asseturl+filesModal.content.map" frameborder="0"></iframe> <br/>
                    <a target="_blank" style="padding: 6px 23px;font-size: 29px;" :href="$asseturl+filesModal.content.map" class="btn btn-info text-center">ডাউনলোড</a>
                </div>



                <div class="col-md-12 form_title">
                    <h5>ওয়ারিশান সনদপত্রের কপি</h5>
                </div>

                <div class="col-md-12 text-center">
                    <iframe     width="100%" height="300px"  :src="$asseturl+filesModal.content.wyarisan" frameborder="0"></iframe> <br/>
                    <a target="_blank" style="padding: 6px 23px;font-size: 29px;" :href="$asseturl+filesModal.content.wyarisan" class="btn btn-info text-center">ডাউনলোড</a>
                </div>




            </div>


            <template #modal-footer>
<div></div>
      </template>
        </b-modal>




        <!-- Info modal -->
        <b-modal :id="DakhilModal.id" size="xl" >

                <Prodibedondakhil :approve-data="DakhilModal.status" :sonod-id="DakhilModal.content_id"
                    :Details="DakhilModal.content" @event-name="sonodList">
                </Prodibedondakhil>





            <template #modal-footer>
<div></div>
      </template>
        </b-modal>

        <!-- Info modal -->
        <b-modal :id="actionModal.id" size="xl" :title="actionModal.title" >
            <div v-if="actionModal.title == 'Cancel'">
                <form v-on:submit.prevent="formcancel">
                    <div class="form-group">
                        <label for="">বাতিল এর কারন উল্লেখ করুন</label>
                        <textarea class="form-control" v-model="b.reson" cols="30" rows="10"
                            style="height:100px;resize:none"></textarea>
                    </div>
                    <button type="submit" class="btn btn-info">Not Approve</button>
                </form>
            </div>
            <div v-else>
                <approvetrade :approve-data="actionModal.status" :sonod-id="actionModal.content_id"
                    :Details="actionModal.content" @event-name="sonodList" v-if="SonodName.enname == 'Trade license'">
                </approvetrade>
                <approvesonod :approve-data="actionModal.status" :sonod-id="actionModal.content_id"
                    :Details="actionModal.content" @event-name="sonodList" v-else>
                </approvesonod>
            </div>



            <template #modal-footer>
<div></div>
      </template>
        </b-modal>




    </div>
</template>
<script>
export default {
    computed: {
    },
    created() {
        if (this.$route.params.type == 'cancel') {
            this.fields.push({ key: 'cancedby', label: 'বাতিল করেছে', sortable: true },);
        }
    },
    data() {
        return {
            searching: false,
            preLooding: true,
            nidverify: false,
            dobverify: false,
            buttonLoader: false,
            Type: '',
            sonod_id: '',
            Vaccination: '',
            editRoute: '',
            viewRoute: '',
            cancelRoute: '',
            approveRoute: '',
            approveType: '',
            payRoute: '',
            applicationRoute: '',
            applicationRoute2: '',
            items: [],
            f: {
                paymentType: '',
                district: '',
            },
            b: {
                reson: '',
            },

            infoModal: {
                id: 'info-modal',
                title: '',
                content: {},
                content_id: '',
            },

            filesModal: {
                id: 'files-modal',
                title: '',
                content: {},
                content_id: '',
            },

            DakhilModal: {
                id: 'dakhil-modal',
                title: '',
                content: {},
                content_id: '',
            },

            prottoyonModal: {
                id: 'prottoyon-modal',
                title: '',
                content: {},
                content_id: '',
            },
            actionModal: {
                id: 'action-modal',
                title: '',
                status: '',
                content: {},
                content_id: '',
            },
            PerPageData: '20',
            TotalRows: '1',
            Totalpage: {},
        }
    },
    watch: {
        '$route': {
            handler(newValue, oldValue) {
                this.sonodList();
            },
            deep: true
        }
    },
    methods: {
        searchSondId() {
            this.searching = true
            this.sonodList(true, this.sonod_id)
        },



        async info(item, index, button) {
            this.buttonLoader = true;
            this.infoModal.title = `${item.name}`
            this.infoModal.content = item
            this.buttonLoader = false;
            this.$root.$emit('bv::show::modal', this.infoModal.id, button)
        },


        async filesView(item, index, button) {

            this.buttonLoader = true;
            this.filesModal.title = `${item.name}`
            this.filesModal.content = item
            this.buttonLoader = false;
            this.$root.$emit('bv::show::modal', this.filesModal.id, button)
        },



        async approve(route, item, status,confirm,success,button,modal='') {

            if(modal=='modal'){

                    this.DakhilModal.title = `${item.name}`
                    this.DakhilModal.content = item
                    this.$root.$emit('bv::show::modal', this.DakhilModal.id, button)

            }else{
                Swal.fire({
                        title: 'আপনি কি নিশ্চিত?',
                        text: `${confirm}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: `হা নিশ্চিত`,
                        cancelButtonText: `বাতিল`
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            var res = await this.callApi('get', `${route}/${status}/${item.id}`, []);
                            Notification.customSuccess(`${success}`);
                            this.preLooding = false
                            this.sonodList()
                        } else {
                            this.preLooding = false
                        }
                    })
            }






        },

        async cancel(route, id, status, button) {
            // console.log(id)
            this.actionModal.content_id = `${id}`;
            this.actionModal.title = `Cancel`;
            this.$root.$emit('bv::show::modal', this.actionModal.id, button)
        },
        async formcancel() {
            var id = this.actionModal.content_id;
            this.b['names'] = this.Users.names;
            this.b['user_id'] = this.Users.id;
            this.b['position'] = this.Users.position;
            this.b['unioun'] = localStorage.getItem('unioun');
            this.b['status'] = 'cancel';
            this.b['sonod_id'] = id;
            var res = await this.callApi('post', `${this.cancelRoute}/cancel/${id}`, this.b);
            // console.log(res)
            this.$root.$emit('bv::hide::modal', this.actionModal.id)
            this.sonodList()
            this.actionModal.content_id = ''
            this.actionModal.title = ''
            this.actionModal.content = {}
            this.b = {
                reson: '',
            }
            Notification.customSuccess(`Your data has been Canceled`);
        },


        async sonodList(auto = false, sondId = '') {

            if (!auto) this.preLooding = true
            var page = 1;
            if (this.$route.query.page) page = this.$route.query.page;
                    var  stutus = this.$route.params.type;
                if (sondId) {
                    var res = await this.callApi('get', `/api/sonod/list?stutus=${this.$route.params.type}&id_no=${sondId}`, []);
                } else {
                    var res = await this.callApi('get', `/api/sonod/list?page=${page}&stutus=${this.$route.params.type}`, []);
                }
                this.items = res.data.data
                this.TotalRows = `${res.data.total}`;
                console.log(res.data.total)
                this.Totalpage = res.data.links
                if (!auto) window.scrollTo(0, 0);
                if (!auto) this.preLooding = false
                this.searching = false
        },

    },
    mounted() {


            this.sonodList();

        setInterval(() => {
            this.sonodList(true,this.sonod_id)
        }, 300000);
    }
}
</script>
<style scoped>
th.position-relative {
    font-size: 13px;
}
th {
    font-size: 11px;
}
td {
    font-size: 12px !important;
}
li.page-item.active a {
    color: white !important;
}










@media
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

	/* Force table to not be like tables anymore */
	.sonodTable, .sonodThead, .sonodTbody, .sonodTh, .sonodTd, .sonodTr {
		display: block;
	}

	/* Hide table headers (but not display: none;, for accessibility) */
	.sonodThead .sonodTr {
		position: absolute;
		top: -9999px;
		left: -9999px;
	}

	.sonodTr { border: 1px solid #ccc; }

    .sonodTr:nth-child(odd) {
      background: #ccc;
    }
	.sonodTd {
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #eee;
		position: relative;
        padding-top: 12px;
    padding-bottom: 12px;
		padding-left: 50%;
	}

	.sonodTd:before {
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%;
		padding-right: 10px;
		white-space: nowrap;
	}

	/*
	Label the data
	*/
	.sonodTd:nth-of-type(1):before { content: "সনদ নাম্বার"; }
	.sonodTd:nth-of-type(2):before { content: "নাম"; }
	.sonodTd:nth-of-type(3):before { content: "পিতার/স্বামীর নাম"; }
	.sonodTd:nth-of-type(4):before { content: "গ্রাম/মহল্লা"; }
	.sonodTd:nth-of-type(5):before { content: "আবেদনের তারিখ"; }
	.sonodTd:nth-of-type(6):before { content: "কার্যক্রম"; }
	.sonodTd:nth-of-type(7):before { content: "ফি"; }

}




</style>
