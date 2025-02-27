export default function CustomButton({
  onClick,
  buttonText,
  btnColor = "primary",
  borderColor = "primary-dark",
  textColor = "primary",
  hoverColor = "primary-dark",
  hoverTextColor = "white",
  fontFamily = "font-poppins", // Custom font
  fontSize = "text-base",
  fontWeight = "font-normal",
  lineHeight = "leading-normal",
  textTransform = "capitalize",
  responsiveTextSize = "text-base",
  className,
  width = "min-w-[120px]", // Default width
  ...props
}) {
  return (
    <div className="text-center">
      <button
        type="button"
        onClick={onClick}
        className={`
          ${width} 
          flex items-center justify-center
          bg-${btnColor} 
          text-${textColor} 
          ${responsiveTextSize} 
          px-[20px] py-[8px] 
          ${fontWeight} ${fontFamily} ${fontSize} ${lineHeight} 
          ${textTransform} tracking-wider 
          border-2 border-${borderColor} 
          rounded-full 
          hover:bg-${hoverColor} hover:text-${hoverTextColor} 
          ${className || ""}
        `}
        {...props}
      >
        {buttonText}
      </button>
    </div>
  );
}
