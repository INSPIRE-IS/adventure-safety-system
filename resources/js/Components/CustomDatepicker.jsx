import React, { useEffect, useState } from "react";
import DatePicker from "react-date-picker";
import "react-date-picker/dist/DatePicker.css"; // Optional, for default styling

const CustomDatePicker = ({ label, name, value, onChange, mode, error }) => {
  const [date, setDate] = useState('');

    useEffect(() => {
        if (value && mode !== "create") {
            setDate(value);
            console.log('value', value);
        } else if (mode === "create") {
          //const date = new Date();
          //setDate(date.toISOString().slice(0, 10));
          //reset(); // Clear form for create mode
        }
    }, [mode,value]);

  const handleChange = (dateValue) => {
    setDate(dateValue);
    onChange(name,dateValue); // Pass the selected date to the parent component
  };

  return (
    <div className="w-full mb-4">
      {label && <label className="block input-header mb-[14px]">{label}</label>}
      <DatePicker
        value={date}
        onChange={handleChange}
        format="dd/MM/yyyy" // Set the format to dd/MM/yyyy
        className={`mt-1 pt-[5px] pb-[5px] block w-full focus:outline-none focus:ring-2 ${error ? "border-red-500" : "border-gray-300"} 
        shadow-sm input-text border rounded-xl border-gray-300 focus:ring-blue-300`}
        disabled={mode === "view"}

      />
      {error && <p className="text-red-500 text-sm mt-1">{error}</p>}
    </div>
  );
};
        // className={`w-full pl-[19px] pt-[11px] pb-[12px] bg-[#FBF9F7] border rounded-full focus:outline-none focus:ring-2 ${
        //   error ? "border-red-500 focus:ring-red-300 text-red-700" : "border-gray-300 focus:ring-blue-300 text-main2"
        // }`}
export default CustomDatePicker;
