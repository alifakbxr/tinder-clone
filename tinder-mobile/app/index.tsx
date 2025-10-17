import React from 'react';
import { RecoilRoot } from 'recoil';
import AppNavigator from '../src/navigation/AppNavigator';

export default function AppEntry() {
  return (
    <RecoilRoot>
      <AppNavigator />
    </RecoilRoot>
  );
}