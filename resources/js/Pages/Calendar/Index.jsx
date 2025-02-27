import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from "@inertiajs/react";


const CalendarIndex = ({auth,events}) => {
    // const loadLocalScript = (src) => {
    //     return new Promise((resolve) => {
    //         const script = document.createElement("script");
    //         script.src = src;
    //         script.async = true;
    //         script.onload = resolve;
    //         document.body.appendChild(script);
    //     });
    // };
    
    const loadLocalCSS = (href) => {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = href;
        document.head.appendChild(link);
    };
    const loadExternalScript = (url) => {
        return new Promise((resolve) => {
            const script = document.createElement("script");
            script.src = url;
            script.async = true;
            script.onload = resolve;
            document.body.appendChild(script);
        });
    };
    useEffect(() => {
        loadExternalScript("https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js")
            .then(() => loadExternalScript("https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"))
            .then(() => loadExternalScript("https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js"))
            .then(() => {
                $("#calendar").fullCalendar({ events: events });
            });
            loadLocalCSS("/css/fullcalendar3.1.0.min.css");
        return () => {
            if (window.$) {
                $("#calendar").fullCalendar("destroy");
            }
        };
    }, []);

    // useEffect(() => {
    //     loadLocalScript("/js/jquery3.3.1.min.js")
    //         .then(() => loadLocalScript("/js/moment2.17.1.min.js"))
    //         .then(() => loadLocalScript("/js/fullcalendar3.1.0.min.js"))
    //         .then(() => {
    //             $("#calendar").fullCalendar({ events: events });
    //         });

    //     return () => {
    //         if (window.$) {
    //             $("#calendar").fullCalendar("destroy");
    //         }
    //     };
    // }, []);
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Calendar" />
            <div className="container mx-auto py-6 h-[400px]">
            {/* Header */}
                <header className="mb-6 flex items-center justify-between w-[95%]">
                    <div className="flex flex-col  items-right">               
                        <h1 className="view-header">Calendar Inspection Drill Post-Incident Schedules</h1>   
                
                    </div>
                        <Link href="/dashboard" className='header-links hover:text-blue-500 cursor-pointer'>
                            DASHBOARD
                        </Link>                     
                </header>
                <div className="overflow-x-auto bg-white rounded-lg p-6 w-[100%] h-[400px]">
                    <div className='w-[100%] h-[400px]' id="calendar"></div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
};

export default CalendarIndex;
