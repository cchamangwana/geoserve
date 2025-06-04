<template>
    <app-layout>
        <template #header>
            Dashboard
        </template>

        <template #breadcrumbs>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="heading-font uppercase text-sm font-medium text-gray-500 dark:text-gray-400">Dashboard</span>
                </div>
            </li>
        </template>

        <template #actions v-if="!checkRole($page.props.auth.data,'unverified') && !checkRole($page.props.auth.data,'disabled')">
            <inertia-link :href="route('request-forms.create')">
                <primary-button>
                    New Request
                </primary-button>
            </inertia-link>
            <secondary-button @click.native="findRequestDialog=true" style="padding-top: 0.4rem; padding-bottom: 0.4rem;">
                Find Request
                <i class="ml-2 mdi mdi-magnify text-lg"></i>
            </secondary-button>
        </template>

        <dialog-modal :show="findRequestDialog" @close="findRequestDialog=false">
            <template #title>
                Find Request
            </template>

            <template #content>

                <div class="mb-4">
                    <jet-label for="requestCode" value="Enter Request Code" />
                    <jet-input id="requestCode" type="text" class="mt-1 block w-full" v-model="form.requestCode" autocomplete="geoserve-request-code"/>
                    <span v-show="form.requestCode.length !==8" class="text-xs text-red-600">Enter 8 Characters</span>
                </div>
            </template>

            <template #footer>
                <secondary-button @click.native="findRequestDialog=false">
                    Cancel
                </secondary-button>

                <primary-button class="ml-2" @click.native="findRequest" :disabled="form.processing || form.requestCode.length !==8">
                    <svg v-show="form.processing" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                    </svg>
                    Search
                </primary-button>
            </template>
        </dialog-modal>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">

                <div v-if="checkRole($page.props.auth.data,'unverified')">

<!--                    <div class="text-sm text-center">Awaiting Verification</div>-->
                    <div class=" bg-white shadow-md overflow-hidden rounded-xl">
                        <div class="px-6 py-4">
                            <img class="mx-auto max-h-96" :src="fileUrl('images/access-blocked.svg')" alt="Access Blocked">
                        </div>
                        <div class="px-6 py-4 heading-font bg-blue-700 text-center text-lg text-white">
                            <svg role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                            </svg>
                            Awaiting Verification
                        </div>
                    </div>
                </div>
                <div v-else-if="checkRole($page.props.auth.data,'disabled')">
                    <div class=" bg-white shadow-md overflow-hidden rounded-xl">
                        <div class="px-6 py-4">
                            <img class="mx-auto max-h-96" :src="fileUrl('images/access-blocked.svg')" alt="Access Blocked">
                        </div>
                        <div class="px-6 py-4 heading-font bg-rose-600 text-center text-lg text-white">
                            Your account is disabled
                        </div>
                    </div>

                </div>
                <div v-else>
                    <div v-if="dashboardReports.data.length > 0" class="page-section">
                        <div class="page-section-header">
                            <div class="page-section-title">
                                Overview
                            </div>
                        </div>
                        <div class="page-section-content">
                            <div class="card">
                                <div class="md:p-8 w-full  mx-auto relative">
                                    <BarChart
                                        :chart-options="positionsChartOptions"
                                        :chart-data="positionsData"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                <div class="card" v-if="awaitingApprovalCount>0">
                                    <div class="flex justify-start items-center">
                                        <div class="overview-chart relative">
                                            <div style="font-size:12px;" class="absolute h-full w-full font-bold flex justify-center items-center">
                                                {{ Math.floor((awaitingApprovalCount/totalCount)*100) }}%
                                            </div>
                                            <DoughnutChart
                                                :chart-options="chartOptions"
                                                :chart-data="awaitingApprovalData"
                                                chart-id="awaitingApproval"
                                                dataset-id-key="awaitingApproval"
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div class="heading-font" style="font-weight: 600;">Awaiting Approval</div>
                                            <div class="text-sm text-gray-400">{{ awaitingApprovalCount }} {{ awaitingApprovalCount ==1?'Request':'Requests'}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card" v-if="awaitingInitiationCount>0">
                                    <div class="flex justify-start items-center">
                                        <div class="overview-chart relative">
                                            <div style="font-size:12px;" class="absolute h-full w-full font-bold flex justify-center items-center">
                                                {{ Math.floor((awaitingInitiationCount/totalCount)*100) }}%
                                            </div>
                                            <DoughnutChart
                                                :chart-options="chartOptions"
                                                :chart-data="awaitingInitiationData"
                                                chart-id="awaitingInitiation"
                                                dataset-id-key="awaitingInitiation"
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div class="heading-font" style="font-weight: 600;">Awaiting Initiation</div>
                                            <div class="text-sm text-gray-400">{{ awaitingInitiationCount }} {{ awaitingInitiationCount ==1?'Request':'Requests'}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card" v-if="awaitingReconciliationCount>0">
                                    <div class="flex justify-start items-center">
                                        <div class="overview-chart relative">
                                            <div style="font-size:12px;" class="absolute h-full w-full font-bold flex justify-center items-center">
                                                {{ Math.floor((awaitingReconciliationCount/totalCount)*100) }}%
                                            </div>
                                            <DoughnutChart
                                                :chart-options="chartOptions"
                                                :chart-data="awaitingReconciliationData"
                                                chart-id="awaitingReconciliation"
                                                dataset-id-key="awaitingReconciliation"
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div class="heading-font" style="font-weight: 600;">Awaiting Reconciliation</div>
                                            <div class="text-sm text-gray-400">{{ awaitingReconciliationCount }} {{ awaitingReconciliationCount ==1?'Request':'Requests'}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card" v-if="activeCount>0">
                                    <div class="flex justify-start items-center">
                                        <div class="overview-chart relative">
                                            <div style="font-size:12px;" class="absolute h-full w-full font-bold flex justify-center items-center">
                                                {{ Math.floor((activeCount/totalCount)*100) }}%
                                            </div>
                                            <DoughnutChart
                                                :chart-options="chartOptions"
                                                :chart-data="activeData"
                                                chart-id="active"
                                                dataset-id-key="active"
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div class="heading-font" style="font-weight: 600;">Active</div>
                                            <div class="text-sm text-gray-400">{{ activeCount }} {{ activeCount ==1?'Request':'Requests'}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div  class="card" v-if="unverifiedUsersCount>0 && (checkRole($page.props.auth.data,'management') || checkRole($page.props.auth.data,'administrator'))">
                                    <inertia-link :href="route('users')">
                                        <div class="flex justify-start items-center">
                                            <div class="ml-3 mr-1 relative">
                                                <i class="mdi mdi-account-supervisor-circle" style="font-size: 32px; color:#eab308"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="heading-font" style="font-weight: 600;">Unverified Users</div>
                                                <div class="text-sm text-gray-400">{{unverifiedUsersCount}} {{unverifiedUsersCount==1?'User':'Users'}}</div>
                                            </div>
                                        </div>
                                    </inertia-link>
                                </div>
                                <div class="card" v-if="unverifiedProjectsCount>0 && (checkRole($page.props.auth.data,'management') || checkRole($page.props.auth.data,'administrator'))">
                                    <inertia-link :href="route('projects')">
                                        <div class="flex justify-start items-center">
                                            <div class="ml-3 mr-1 relative">
                                                <i class="mdi mdi-home-group" style="font-size: 32px; color:#eab308"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="heading-font" style="font-weight: 600;">Unverified Projects</div>
                                                <div class="text-sm text-gray-400">{{unverifiedProjectsCount}} {{unverifiedProjectsCount==1?'Project':'Projects'}}</div>
                                            </div>
                                        </div>
                                    </inertia-link>
                                </div>
                                <div class="card" v-if="unverifiedVehiclesCount>0 && (checkRole($page.props.auth.data,'management') || checkRole($page.props.auth.data,'administrator'))">
                                    <inertia-link :href="route('vehicles')">
                                        <div class="flex justify-start items-center">
                                            <div class="ml-3 mr-1 relative">
                                                <i class="mdi mdi-car-multiple" style="font-size: 32px; color:#eab308"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="heading-font" style="font-weight: 600;">Unverified Vehicles</div>
                                                <div class="text-sm text-gray-400">{{unverifiedVehiclesCount}} {{unverifiedVehiclesCount==1?'Vehicle':'Vehicles'}}</div>
                                            </div>
                                        </div>
                                    </inertia-link>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="page-section">
                        <div class="page-section-header">
                            <div class="page-section-title">
                                Awaiting your action
                            </div>
                        </div>
                        <div class="page-section-content">
                            <div class="grid grid-cols-1 md:grid-cols-2">
                                <request
                                    v-for="(request,index) in toApprove.data"
                                    :key="index"
                                    :request="request"
                                />
                                <div v-if="toApprove.data.length === 0" class="text-center text-gray-400 md:col-span-2 text-sm">
                                    No Requests To Approve
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-section">
                        <div class="page-section-header">
                            <div class="page-section-title">
                                Active Requests
                            </div>
                        </div>
                        <div class="page-section-content">
                            <div class="grid grid-cols-1 md:grid-cols-2">
                                <request
                                    v-for="(request,index) in active.data"
                                    :key="index"
                                    :request="request"
                                />
                                <div v-if="active.data.length === 0" class="text-center text-gray-400 md:col-span-2 text-sm">
                                    No Active Requests
                                </div>
                            </div>
                            <!-- Pagination Links -->
                            <div v-if="active.links && active.links.length > 3" class="mt-4 flex justify-center">
                                <div class="flex flex-wrap -mb-1">
                                    <template v-for="(link, key) in active.links" :key="key">
                                        <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                                        <inertia-link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url" v-html="link.label" />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import DoughnutChart from "@/Components/Charts/DoughnutChart";
    import BarChart from "@/Components/Charts/BarChart";
    import PrimaryButton from '@/Jetstream/Button'
    import SecondaryButton from "@/Jetstream/SecondaryButton";
    import Request from "@/Components/Request";
    import DialogModal from "@/Jetstream/DialogModal";
    import JetLabel from "@/Jetstream/Label";
    import JetInput from "@/Jetstream/Input";
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        props:[
            'toApprove',
            'active',
            'awaitingApprovalCount',
            'awaitingInitiationCount',
            'awaitingReconciliationCount',
            'activeCount',
            'totalCount',
            'unverifiedUsersCount',
            'unverifiedVehiclesCount',
            'unverifiedProjectsCount',
            'dashboardReports',
        ],
        components: {
            AppLayout,
            DoughnutChart,
            BarChart,
            PrimaryButton,
            SecondaryButton,
            Request,
            DialogModal,
            JetLabel,
            JetInput,
            JetValidationErrors,
        },
        data(){
          return{
              findRequestDialog:false,
              error:false,
              form: this.$inertia.form({
                  requestCode:'',
              }),
              chartOptions:{
                  plugins: {
                      tooltip: {
                          enabled: false
                      },
                      legend:{
                          display:false
                      }
                  }, cutout:20
              },
              awaitingApprovalData:{
                  datasets: [{
                      data: [this.awaitingApprovalCount, (this.totalCount - this.awaitingApprovalCount)],
                      backgroundColor: ['#eab308','#e3ebf6'],
                  }],
              },
              awaitingInitiationData:{
                  datasets: [{
                      data: [this.awaitingInitiationCount, (this.totalCount - this.awaitingInitiationCount)],
                      backgroundColor: ['#22c55e','#e3ebf6'],
                  }],
              },
              awaitingReconciliationData:{
                  datasets: [{
                      data: [this.awaitingReconciliationCount, (this.totalCount - this.awaitingReconciliationCount)],
                      backgroundColor: ['#22c55e','#e3ebf6'],
                  }],
              },
              activeData:{
                  datasets: [{
                      data: [this.activeCount, (this.totalCount - this.activeCount)],
                      backgroundColor: ['#1a56db','#e3ebf6'],
                  }],
              },
              positionsData:{
                  datasets: [{
                      data: [],
                      backgroundColor: ['#1a56db','#ed0b4b','#b1bbc9','#e3ebf6'],

                  }],
                  labels: []
              },
              positionsChartOptions:{
                  plugins: {
                      tooltip: {
                          enabled: true
                      },
                      legend:{
                          display:false,
                      }
                  },
                  scales: {
                      xAxes: {
                          grid: {
                              display:false
                          }
                      },
                      yAxes: {
                          grid: {
                              display:false
                          }
                      },
                  },
                  maintainAspectRatio:false
              },
          }
        },

        created() {
            for(let x in this.dashboardReports.data) {
                console.log(this.dashboardReports.data[x])
                this.positionsData.datasets[0].data.push(this.dashboardReports.data[x].requestsCount)
                this.positionsData.labels.push(this.dashboardReports.data[x].month)
            }
        },
        methods:{
            findRequest(){
                this.form
                    .post(this.route('request-forms.find',{code:this.form.requestCode}),{
                        onSuccess: () => this.findRequestDialog=false,
                    })
            },
        }
    }
</script>
