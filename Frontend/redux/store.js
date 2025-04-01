import { configureStore } from "@reduxjs/toolkit";
import healthReducer from "./healthSlice";

export default configureStore({
  reducer: {
    health: healthReducer,
  },
});
