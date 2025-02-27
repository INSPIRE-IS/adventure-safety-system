import React from "react";

const CustomSelect1 = ({ label, name, value, onChange, error,mode, options }) => {
  return (
    <div className="w-full mb-4">
      {label && (
        <label className="block input-header mb-[14px]">
          {label}
        </label>
      )}
      <select
        name={name}
        value={value}
        onChange={onChange}
        className={`mt-1 block w-full ${error ? "border-red-500" : "border-gray-300"} shadow-sm input-text rounded-xl`}
        disabled={mode === "view"}

      >
        {/* Render the options */}
        <option value="" disabled className="input-text">
          -- Select an option --
        </option>
        {options.map((option, index) => (
          <option key={index} value={option.value} className="input-text">
            {option.label}
          </option>
        ))}
      </select>
      {error && <p className="text-red-500 text-sm mt-1">{error}</p>}
    </div>
  );
};
        // className={`w-full pl-[19px] pt-[11px] pb-[12px] bd-[#FBF9F7] border rounded-full focus:outline-none focus:ring-2 ${
        //   error
        //     ? "border-red-500 focus:ring-red-300 text-red-700"
        //     : "border-gray-300 focus:ring-blue-300 text-main2"
        // }`}
        // style={{
        //     backgroundColor: "#FBF9F7", // Explicitly set the background color in case Tailwind is overridden
        //   }}
export default CustomSelect1;
