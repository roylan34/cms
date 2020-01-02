import React, { useState, useEffect, useRef } from "react";
import FullCalendar from "@fullcalendar/react";
import dayGridPlugin from "@fullcalendar/daygrid";
import moment from 'moment';
import DashboardServices from './../../services/dashboardServices';
import { Col, Row } from 'antd';
import Jwt from './../../helpers/jwt';

//Manually import the stylesheets for each plugin
import "@fullcalendar/core/main.css";
import "@fullcalendar/daygrid/main.css";

import './dashboard-style.css';
export default function Dashboard() {
    let calendarComponentRef = useRef();

    const [state, setState] = useState({
        calendarDateNow: moment().format('YYYY-MM'),
        calendarEvents: [],
        statusCount: { active: 0, notifying: 0, expired: 0 }
    });

    function calendarForecast(calendarDateNow) {
        const user_id = Jwt.get('id');
        const date_now = calendarDateNow;
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

    function statusCount() {
        const user_id = Jwt.get('id');
        const data_count = DashboardServices.getStatusCount(user_id);
        setState((prev) => ({
            ...prev,
            statusCount: data_count.aaData[0]
        }));
    }

    useEffect(() => {
        const stateDateNow = state.calendarDateNow;
        calendarForecast(stateDateNow);
        calendarPrevNext();
        calendarToday();
    }, [state.calendarDateNow]);


    useEffect(() => {
        statusCount();
    }, [])

    return (
        <React.Fragment>
            <Row className="expiration-info-box" type="flex" gutter={[8, 0]}>
                <Col md={8} xs={24}>
                    <div className="small-box bg-green">
                        <div className="inner">
                            {/* <h3>{state.statusCount.active}</h3>
                            <p>Active</p> */}
                            <div className="count">Active: <span>{state.statusCount.active}</span></div>
                        </div>
                        <div className="icon">
                            <i className="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </Col>
                <Col md={8} xs={24}>
                    <div className="small-box bg-yellow">
                        <div className="inner">
                            {/* <h3>{state.statusCount.notifying}</h3>
                            <p>Nofiying</p> */}
                            <div className="count">Nofiying: <span>{state.statusCount.notifying}</span></div>
                        </div>
                        <div className="icon">
                            <i className="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </Col>
                <Col md={8} xs={24}>
                    <div className="small-box bg-red">
                        <div className="inner">
                            {/* <h3>{state.statusCount.expired}</h3>
                            <p>Expired</p> */}
                            <div className="count">Expired: <span>{state.statusCount.expired}</span></div>
                        </div>
                        <div className="icon">
                            <i className="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </Col>
            </Row>

            <div className="expiration-calendar">
                <p className="text-left">Monthly Forecast</p>
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
        </React.Fragment>
    );

}
