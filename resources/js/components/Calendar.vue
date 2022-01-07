<template>
    <div class="calendar-container mt-5">
        <div class="calendar">
            <div class="calendar__header">
                <h1>{{ getMonth }} {{ year }}</h1>
            </div>
            <div class="calendar__body">
                <div class="calendar__content py-2 border-top" v-for="(date, index) in dates" :key="index">
                    <div class="calendar__date">{{ date.date }} <span>{{ date.day }}</span></div>
                    <div class="calendar__event" v-for="(e, i) in date.event" :key="i">{{ e }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'dates',
        'year',
        'month',
    ],

    computed: {
        getMonth() {
            const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            const d = new Date(this.year, this.month);
            return months[d.getMonth()];
        }
    }
}
</script>

<style lang="scss" scoped>
    span {
        font-weight: bolder;
    }
</style>
