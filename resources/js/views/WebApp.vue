<template>
    <div class="container-fluid d-flex p-5 wrap">
        <div class="form px-3">
            <form @submit.prevent="submit">
                <div class="form-group">
                    <label for="event">Event</label>
                    <input type="text" class="form-control" v-model="form.event" required>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="event">from</label>
                        <input
                            type="date"
                            class="form-control"
                            @change="formDateMutation"
                            v-model="form.from_date"
                            required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="event">to</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="form.to_date"
                            @change="validateEndDate"
                            required>

                        <small
                            class="text-danger"
                            v-if="errors.to_date"
                            v-text="typeof errors.to_date == 'string' ? errors.to_date : errors.to_date[0]"
                        ></small>
                    </div>
                    <div class="form-check form-check-inline mb-3">
                        <template v-for="day in days">
                            <div :key="day">
                                <input class="form-check-input" type="checkbox" :id="day" :value="day" v-model="form.days" required>
                                <label class="form-check-label mr-2" :for="day">{{ day }}</label>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
        <calendar-component :dates="eventDates" :year="year" :month="month"></calendar-component>
    </div>
</template>

<script>
import Calendar from '../components/Calendar';

function initialFrom() {
    return {
        event: null,
        from_date: null,
        to_date: null,
        days: []
    }
}

export default {
    components: {
        "calendar-component": Calendar
    },

    data() {
        return {
            daysInMonth: 0,
            year: '',
            month: '',
            day: '',
            form: initialFrom(),
            days: ["Mon","Tues","Wed","Thurs","Fri","Sat","Sun"],
            events: {},
            errors: {},
            eventDates: [],
            range: 0
        }
    },

    async created() {
        await this.getEvents();
        this.getSelectedMonth();
        this.getSelectedDays();
        this.getSelectedYear();
        this.getDaysInMonth(Number(moment().format('M')), Number(moment().format('YYYY')));
    },

    methods: {
        getDaysInMonth(month, year) {
            this.daysInMonth = new Date(year, month, 0).getDate();
        },

        getSelectedMonth() {
            const d = new Date();
            this.month = d.getMonth();
        },

        getSelectedDays() {
            const d = new Date();
            this.day = d.getDate();
        },

        getSelectedYear() {
            const d = new Date();
            this.year = d.getFullYear();
        },

        validateEndDate(event) {
            const selectedDate = this.moment(event.target.value).format("YYYY-MM-DD");
            const startDate = this.moment(this.form.from_date).format("YYYY-MM-DD");

            if (selectedDate < startDate && selectedDate !== startDate) {
                return this.$set(
                    this.errors,
                    "to_date",
                    "The to date must not be less than from date"
                );
            }

            return this.$delete(this.errors, "to_date");
        },

        submit() {
            axios
                .post(`/save`, this.form)
                .then(({ data }) => {
                    this.getEvents();
                    this.form = initialFrom();
                })
                .catch(({ response }) => {
                    console.log(response)
                })
        },

        getEvents() {
            axios
                .get('/get-events')
                .then(({ data }) => {
                    this.events = data;
                    if (Object.keys(this.events).length > 0) {
                        this.year = Number(moment(data.from_date).format('YYYY'));
                        this.month = Number(moment(data.from_date).format('M')) - 1
                    }
                })
                .catch(({ response }) => {
                    console.log(response)
                })
                .finally(() => {
                    this.dates();
                })
        },

        dates() {
            var getDates = [];
            const days = ["Sun","Mon","Tues","Wed","Thurs","Fri","Sat"];
            if (Object.keys(this.events).length > 0) {

                for (let index = 1; index <= this.daysInMonth; index++) {
                    const d = new Date(this.year, this.month, index);
                    getDates.push({
                        date: index,
                        day: days[d.getDay()],
                        event: []
                    })
                }

                for (let e = 0; e < Object.keys(this.events).length; e++) {
                    var from = Number(moment(this.events[e].from_date).format('D'));
                    for (let index = 1; index <= this.daysInMonth; index++) {
                        const d = new Date(this.year, this.month, index);
                        this.dateRange(d, this.events[e].to_date);
                        if (index >= from && this.range >= 0 && Object.keys(this.events[e]).length > 0 && this.events[e].days.includes(days[d.getDay()])) {
                            getDates[index - 1].event.push(this.events[e].event)
                        }
                    }
                }
                this.eventDates = getDates;

            }else {
                var from = Number(moment(this.events.from_date).format('D'));

                for (let index = 1; index <= this.daysInMonth; index++) {
                    const d = new Date(this.year, this.month, index);
                    getDates.push({
                        date: index,
                        day: days[d.getDay()],
                        event: ''
                    })
                }
                this.eventDates = getDates;
            }
        },

        formDateMutation(event) {
            var selectedY = Number(moment(event.target.value).format('YYYY'))
            var selectedM = Number(moment(event.target.value).format('M'))
            var selectedD = Number(moment(event.target.value).format('D'))
            this.month = selectedM - 1;
            this.day = selectedD;
            this.year = selectedY;
            this.getDaysInMonth(selectedM, selectedY);
            this.dates()
        },

        dateRange(start, end) {
            var from = new Date(start)
            var to = new Date(end)
            var Difference_In_Time = to.getTime() - from.getTime();
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            this.range = Difference_In_Days
        },
    }
}
</script>

<style lang="scss" scoped>
    .container-fluid {
        gap: 15px;
    }
    .form {
        width: 100%;
        max-width: 400px;
    }
    @media only screen and (max-width: 1200px) {
        .wrap {
            flex-wrap: wrap;
        }
    }
</style>