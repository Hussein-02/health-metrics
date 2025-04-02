import { createSlice } from "@reduxjs/toolkit";

const predictionSlice = createSlice({
  name: "predictions",
  initialState: { data: [] },
  reducers: {
    setPredictions: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setPredictions } = predictionSlice.actions;
export default predictionSlice.reducer;
