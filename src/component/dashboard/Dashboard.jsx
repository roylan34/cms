import React, { useState, useEffect, useRef } from "react";
import FullCalendar from "@fullcalendar/react";
import dayGridPlugin from "@fullcalendar/daygrid";
import moment from 'moment';
import DashboardServices from './../../services/dashboardServices';
import Jwt from './../../helpers/jwt';

// must manually import the stylesheets for each plugin
import "@fullcalendar/core/main.css";
import "@fullcalendar/daygrid/main.css";

import './dashboard-style.css';
export default function Dashboard() {
    let calendarComponentRef = useRef();

    const [state, setState] = useState({
        calendarDateNow: moment().format('YYYY-MM'),
        calendarEvents: []
    });

    function calendarForecast() {
        const user_id = Jwt.get('id');
        const date_now = state.calendarDateNow;
        const data = DashboardServices.getCalendarForecast(user_id, date_now);
        const events = data.aaData.map(event => {
            return {
                title: (event.forecast_status + ': ' + event.forecast_count),
                start: event.valid_to,
                className: (event.forecast_status === "expired" ? "fc-content-red" : "fc-content-orange")
            }

        });
        //Set default events
        setState((prev) => ({
            ...prev,
            calendarEvents: events
        }));
    }

    function calendarPrevNext() {
        let selected_date = null;
        let str_selected_date = null;
        setTimeout(() => {
            const btn_prev_next = document.querySelectorAll('.fc-header-toolbar .fc-button-group');
            btn_prev_next[0].addEventListener('click', function (e) {
                e.preventDefault();
                selected_date = calendarComponentRef.current.calendar.getDate();
                str_selected_date = moment(selected_date).format('YYYY-MM');
                //Set state of selected date
                setState((prev) => ({
                    ...prev,
                    calendarDateNow: str_selected_date
                }));
            });
        }, 300);

    }

    function calendarToday() {
        let current_date = null;
        setTimeout(() => {
            const btn_today = document.querySelector('.fc-today-button');
            btn_today.addEventListener('click', function (e) {
                e.preventDefault();
                current_date = moment().format('YYYY-MM');
                //Set state of current date
                setState((prev) => ({
                    ...prev,
                    calendarDateNow: current_date
                }));
            });
        }, 300);
    }

    useEffect(() => {
        calendarForecast();
        calendarPrevNext();
        calendarToday();
    }, [state.calendarDateNow]);

    return (
        <div className="container-calendar">
            <h4 className="text-left">Expiration Forecast</h4>
            <div className="calendar">
                <FullCalendar
                    defaultView="dayGridMonth"
                    header={{
                        left: "prev,next today",
                        center: "title",
                        right: ""
                    }}
                    plugins={[dayGridPlugin]}
                    ref={calendarComponentRef}
                    events={state.calendarEvents}
                    eventClick={(info) => console.log(info)}
                />
            </div>
        </div>
    );

}
