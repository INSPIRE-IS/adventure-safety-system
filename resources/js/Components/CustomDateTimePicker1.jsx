import React, { useEffect, useState } from "react";
import DatePicker from "react-date-picker";
import TimePicker from "react-time-picker"; // Import Time Picker
import "react-date-picker/dist/DatePicker.css"; // Default styles
import "react-time-picker/dist/TimePicker.css"; // Default styles

const CustomDateTimePicker = ({ 
  label, 
  name, 
  value, 
  onChange, 
  mode, 
  type = "date", // "date" or "time"
  error 
}) => {
  const [selectedValue, setSelectedValue] = useState("");

  useEffect(() => {
    if (value && mode !== "create") {
      setSelectedValue(value);
    }
  }, [mode, value]);

  const handleChange = (newValue) => {
    setSelectedValue(newValue);
    onChange(name, newValue); // Pass the selected value to the parent
  };

  return (
    <div className="w-full mb-4">
      {label && <label className="block input-header mb-[14px]">{label}</label>}
      
      {type === "date" ? (
        <DatePicker
          value={selectedValue}
          onChange={handleChange}
          format="dd/MM/yyyy"
          className={`mt-1 pt-[5px] pb-[5px] block w-full focus:outline-none focus:ring-2 ${
            error ? "border-red-500" : "border-gray-300"
          } shadow-sm input-text border rounded-xl border-gray-300 focus:ring-blue-300`}
          disabled={mode === "view"}
        />
      ) : (
        <TimePicker
          value={selectedValue}
          onChange={handleChange}
          format="HH:mm" // 24-hour format
          disableClock={true} // Hides the analog clock UI
          clearIcon={null}
          className={`mt-1 pt-[5px] pb-[5px] block w-full focus:outline-none focus:ring-2 ${
            error ? "border-red-500" : "border-gray-300"
          } shadow-sm input-text border rounded-xl border-gray-300 focus:ring-blue-300`}
          disabled={mode === "view"}
        />
      )}

      {error && <p className="text-red-500 text-sm mt-1">{error}</p>}
    </div>
  );
};

export default CustomDateTimePicker;
