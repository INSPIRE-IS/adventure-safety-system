import React, { useEffect } from "react";



const AlertModal = ({ isOpen, onClose, alerts = null }) => {

    console.log("alerts", alerts);


    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    User Notification

                </h2>
                <label
                        htmlFor="teams"
                        className="block input-header"
                    >Notification/s</label>
                    {alerts && alerts.length > 0 && (
                        <div className="flex flex-col space-y-2 mb-2 max-h-64 overflow-y-scroll">
                            {alerts?.map((alert) => (
                                <div key={alert.id} className="flex flex-row space-x-2 ml-4">
                                <svg fill="#000000" width="64px" height="64px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" strokeWidth="0"></g>
                                    <g id="SVGRepo_tracerCarrier" strokeLinecap="round" strokeLinejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier"> <title>bullet</title> 
                                    <path d="M12.096 16q0 1.632 1.152 2.784t2.752 1.12 2.752-1.12 1.152-2.784-1.152-2.752-2.752-1.152-2.752 1.152-1.152 2.752z"></path> 
                                    </g>
                                </svg>
                                    <label className="font-manrope " htmlFor={alert.id}>{alert.alert_text} <small className="font-anton"> date: {alert.created_at.split(' ')[0]}</small></label>
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="flex justify-end space-x-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                        >
                            Close
                        </button>

                    </div>

            </div>
        </div>
    );
};

export default AlertModal;
