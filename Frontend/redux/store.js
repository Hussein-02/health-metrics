import { configureStore } from "@reduxjs/toolkit";
import healthReducer from "./healthSlice";
import predictionReducer from "./predictionSlice";

export default configureStore({
  reducer: {
    health: healthReducer,
    predictions: predictionReducer,
  },
});
