import React, { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomSelect1 from "@/Components/CustomSelect1";
//import { format } from 'date-fns';
import CustomDatePicker from "@/Components/CustomDatepicker";
import MultiSelect from "@/Components/MultiSelect";
import CustomInput1 from "@/Components/CustomInput1";

const DrillScheduleModal = ({ auth, isOpen, onClose, mode, drillSchedule = null,site, typeActivities, typeDrills,categoryItems, periods, users }) => {
    console.log("mode", mode);
    //console.log("drillSchedule", drillSchedule);
    const [filteredCategoryItems, setFilteredCategoryItems] = useState([]);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        type_activity_id: "",
        site_id: auth?.team?.id,        
        type_drill_id: "",   
       //category_items: [],
        period_id: "",
        active_date: "",
        assigned_by_id: auth?.user?.id,
        assigned_to_id: "",

    });
    useEffect(() => {
        if (drillSchedule && mode !== "create") {
            setData({
                name: drillSchedule.name || "",
                type_activity_id: drillSchedule.type_activity_id || "",
                //category_item_id: drillSchedule.category_item_id || "",
                //category_items: drillSchedule.category_items.map((category_item) => category_item.id) || [], //drillSchedule.category_items.map((category_item) => category_item.id),
                type_drill_id: drillSchedule.type_drill_id || "",
                site_id: drillSchedule.site_id || "",
                period_id: drillSchedule.period_id || "",
                assigned_to_id: drillSchedule.assigned_to_id || "",
                active_date: drillSchedule.active_date || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
            // setData({
            //     name: auth?.team?.name || "",
            // });
        }
    }, [mode,drillSchedule]);


    function formatDate(isoDate) {
        const date = new Date(isoDate);
        //console.log('date', date); return;
        const day = String(date.getDate()).padStart(2, '0');   // Get day and pad with leading zero
        const month = String(date.getMonth() + 1).padStart(2, '0');  // Get month (months are 0-indexed, so add 1) and pad
        const year = date.getFullYear();   // Get full year
    
        return `${day}-${month}-${year}`;  // Return formatted date as DD-MM-YYYY
    }

    const handleChange = (target, value) => {
        //const formattedValue = target === 'active_date' ? format(value, 'dd-MM-yyyy') : value;
        console.log(value);
        //console.log(formattedValue);
        //setData(target, formattedValue);
        setData({ ...data, [target]: value });
        console.log("data", data);
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        console.log("data", data);
        if (mode === "create") {
            post(route("drill-schedules.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
            put(route("drill-schedules.update", drillSchedule.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Drill Schedule"}
                    {mode === "edit" && "Edit Drill Schedule"}
                    {mode === "view" && "View Drill Schedule"}
                </h2>
                <form onSubmit={handleSubmit}>               
                    <label className="block text-lg font-semibold mb-4">
                    {site}
                    </label>
                    <CustomSelect1
                        label="Activity"
                        name="type_activity_id"
                        value={data?.type_activity_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_activity_id}
                        options={typeActivities}
                    />
                    <CustomSelect1
                        label="Type Drill"
                        name="type_drill_id"
                        value={data?.type_drill_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_drill_id}
                        options={typeDrills}
                    />
                    
                    <CustomSelect1
                        label="Assessment Period (set to none to stop the cron job)"
                        name="period_id"
                        value={data?.period_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.period_id}
                        options={periods}
                    /> 
                    <CustomInput1
                        label="Active Date (cron job runs from this date by specified period)"
                        name="active_date"
                        type="date"
                        value={data?.active_date}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.active_date}  
                    />
                    <CustomSelect1
                        label="User"
                        name="assigned_to_id"
                        value={data?.assigned_to_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.assigned_to_id}
                        options={users}
                    />
                    <div className="flex justify-end space-x-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                        {mode !== "view" && (
                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                disabled={processing}
                            >
                                {mode === "create" ? "Create" : "Update"}
                            </button>
                        )}
                    </div>
                </form>
            </div>
        </div>
    );
};

export default DrillScheduleModal;
