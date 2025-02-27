import React from "react";

const CustomDropzoneUploader1 = ({ onUpload, title = "image" }) => {
  const handleDrop = (e) => {
    e.preventDefault(); // Prevent default behavior
    const files = e.dataTransfer.files;
    onUpload(files);
  };

  const handleFileChange = (e) => {
//     console.log('here', e.target.files); 
// return;
    const files = e.target.files;
    onUpload(files); // Call the upload function with selected files
  };

  return (
    <div
      onDrop={handleDrop}
      onDragOver={(e) => e.preventDefault()}
      className="flex flex-col items-center justify-center mt-5 w-[98%] sm:w-[334px]  sm:h-[141px] bg-neutral border-2 rounded-lg border-neutral-light cursor-pointer hover:bg-gray-200"
    >
      {/* Drag Message */}
      <p className="input-text mb-3 text-center">Drag and drop a new {title}</p>

      {/* Upload Button */}
      <div className="flex items-center justify-center">
        <label
          className="bg-white input-text px-6 py-2 border-2 border-primary-dark rounded-full hover:bg-primary-dark hover:text-slate-700 cursor-pointer"
        >
          Or Choose A File
          <input
            type="file"
            multiple
            onChange={handleFileChange}
            className="hidden"
            
            id="file"
            name="file"
          />
        </label>
      </div>

      {/* File Size Message accept="image/*"*/}
      <p className="input-text mt-3 text-center">
        Maximum file size 10mb
      </p>
    </div>
  );
};

export default CustomDropzoneUploader1;
