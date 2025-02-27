import React, { useState } from "react";
import CustomDropzoneUploader1 from "./CustomDropzoneUploader1";
import { useEffect } from "react";

const ImageUpload = ({ currentImage, onUpload, mode, lblText = "Upload Image" }) => {
  const [preview, setPreview] = useState('');
  console.log('currentImage1', currentImage);
  useEffect(() => {
    console.log('currentImage2', currentImage);
    if (currentImage) {
      setPreview(`${"/storage/"}${currentImage?.path}`);
    }else{
      setPreview("./images/document-icon.svg");
    }
  }, [currentImage]);

  const handleFileChange = (files) => {

    const file = files[0];
    // console.log("profile photo", file);
    // const maxSize = 10 * 1024 * 1024;
    // if (file.size > maxSize) {
    //     alert('File size must be less than 10MB.');
    //     return;
    // }


    if (file) {
      currentImage = URL.createObjectURL(file);
      console.log('currentImage3', currentImage);
      onUpload(file);
    }
  };

  return (
    <div className="flex flex-col w-full sm:flex-row items-center mb-4">
      
        <div className="flex flex-col pr-2">
            <label className="mb-[19px]  input-header">{lblText}</label>
            <img
            src={ preview }
            alt="Profile"
            className="rounded-full mb-4 mr-6 object-cover"
            width={45} height={45}
            />            
        </div>
        {mode !== "view" && (
          <CustomDropzoneUploader1 onUpload={handleFileChange} /> 
        )}
                            
    </div>
  );
};

export default ImageUpload;
